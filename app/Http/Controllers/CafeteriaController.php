<?php

namespace App\Http\Controllers;

use League\Csv\Writer;
use App\Models\Cafeteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CafeteriaController extends Controller
{
    const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const CATEGORIES = ['Food', 'Drink', 'Sweet'];

    
    public function home()
    {
        $totalSpent = Cafeteria::sum('amount_spent');
        $remainingBalance = Cafeteria::ANNUAL_LIMIT - $totalSpent;

        $categories = Cafeteria::select('category', \DB::raw('SUM(amount_spent) as sum'))
            ->groupBy('category')
            ->get();

        return view('home', [
            'remainingBalance' => $remainingBalance,
            'categories' => $categories,
        ]);
    }


    public function createExpense(Request $request)
    {
        $rules = [
            'amount_spent' => 'required|numeric|min:0',
            'month' => 'required|string|in:' . implode(',', self::MONTHS),
            'category' => 'required|string|in:' . implode(',', self::CATEGORIES),
        ];

        $request->validate($rules);
        $this->validateAgainstBudgetLimits($request);
        Cafeteria::create($request->all());

        return response()->json(['message' => 'Expense created successfully'], 201);
    }


    public function getExistingBalance()
    {
        $totalSpent = Cafeteria::sum('amount_spent');
        $remainingBalance = Cafeteria::ANNUAL_LIMIT - $totalSpent;

        return response()->json(['remaining_balance' => $remainingBalance], 200);
    }


    public function getCategorySpending()
    {
        $categories = Cafeteria::select('category', \DB::raw('SUM(amount_spent) as sum'))
            ->groupBy('category')
            ->get();

        return response()->json($categories, 200);
    }


    public function getAllSpendings()
    {
        $spendings = Cafeteria::all();

        return response()->json($spendings, 200);
    }


    public function showExpenseForm()
    {
        return view('home');
    }


    public function getExpenseForm()
    {
        return view('expense-form');
    }


    private function validateAgainstBudgetLimits(Request $request)
    {
        // checking total spent 
        $categorySpending = Cafeteria::where('category', $request->category)->sum('amount_spent');
        $totalSpent = Cafeteria::sum('amount_spent');

        // check if spending is negative
        if ($request->amount_spent < 0) {
            throw ValidationException::withMessages(['amount_spent' => 'Expense cannot be negative']);
        }

        // check if the new spending would make the category exceed the limit
        if ($categorySpending + $request->amount_spent > Cafeteria::CATEGORY_LIMIT) {
            throw ValidationException::withMessages(['amount_spent' => 'Expense exceeds category limit']);
        }

        // check if the spending would make a negative annual balance
        if ($totalSpent + $request->amount_spent > Cafeteria::ANNUAL_LIMIT) {
            throw ValidationException::withMessages(['amount_spent' => 'Expense exceeds remaining annual budget']);
        }
    }


    public function submitExpense(Request $request)
    {
        \Log::info('Received request payload:', $request->all());
        
        try {
            $rules = [
                'amount_spent' => 'required|numeric|min:0',
                'month' => 'required|string|in:' . implode(',', self::MONTHS),
                'category' => 'required|string|in:' . implode(',', self::CATEGORIES),
            ];

            $request->validate($rules);
            $this->validateAgainstBudgetLimits($request);
            Cafeteria::create($request->all());

            return response()->json(['message' => 'Expense created successfully'], 201);
        } catch (ValidationException $e) {
            $errors = $e->errors();

            if (isset($errors['amount_spent'])) {
                return response()->json(['error' => $errors['amount_spent'][0]], 422);
            }

            return response()->json(['error' => 'Validation failed'], 422);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }


    public function downloadCsv()
    {
        $spendings = Cafeteria::all();
        $fileName = 'cafeteria_spendings.csv';

        $response = Response::make('', 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ]);

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Amount Spent', 'Month', 'Category', 'Timestamp']);

        foreach ($spendings as $spending) {
            fputcsv($handle, [
                $spending->id,
                $spending->amount_spent,
                $spending->month,
                $spending->category,
                $spending->created_at,
            ]);
        }

        fclose($handle);

        return $response;
    }

}
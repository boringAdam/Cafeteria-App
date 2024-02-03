<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CafeteriaController;


Route::get('/', [CafeteriaController::class, 'showExpenseForm']);
Route::post('/submit-expense', [CafeteriaController::class, 'submitExpense']);
Route::get('/api/get-existing-balance', [CafeteriaController::class, 'getExistingBalance']);
Route::get('/api/get-category-spending', [CafeteriaController::class, 'getCategorySpending']);
Route::get('/api/get-all-spendings', [CafeteriaController::class, 'getAllSpendings']);
Route::get('/download-csv', [CafeteriaController::class, 'downloadCsv']);



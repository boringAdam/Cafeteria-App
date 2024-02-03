<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cafeteria extends Model
{
    const ANNUAL_LIMIT = 400000;
    const CATEGORY_LIMIT = 200000;

    protected $fillable = ['amount_spent', 'month', 'category'];
    protected $table = 'spendings';
    protected $currentBalance;
}

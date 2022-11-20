<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceCategoryClosureModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_category_closure';

    protected $fillable = [
        'id',
        'closure_type',
        'category_id',
        'wallet_id',
    ];

    protected $hidden = [];
}

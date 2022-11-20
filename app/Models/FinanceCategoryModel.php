<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceCategoryModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_category';

    protected $fillable = [
        'id',
        'description',
        'enable',
        'obs',
        'group_id',
        'wallet_id',
        'user_id',
    ];

    protected $hidden = [];
}

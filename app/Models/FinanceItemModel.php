<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceItemModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_item';

    protected $fillable = [
        'id',
        'value',
        'date',
        'obs',
        'sort',
        'enable',
        'origin_id',
        'status_id',
        'type_id',
        'category_id',
        'group_id',
        'wallet_id',
        'user_id',
    ];

    protected $hidden = [];
}

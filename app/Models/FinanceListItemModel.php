<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceListItemModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_list_item';

    protected $fillable = [
        'id',
        'count',
        'value',
        'obs',
        'sort',
        'type_id',
        'category_id',
        'group_id',
        'list_id',
    ];

    protected $hidden = [];
}

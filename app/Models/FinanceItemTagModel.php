<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class FinanceItemTagModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_item_tag';

    protected $fillable = [
        'id',
        'item_id',
        'tag_id',
    ];

    protected $hidden = [];
}

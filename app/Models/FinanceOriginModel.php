<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceOriginModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_origin';
    
    protected $fillable = [
        'id',
        'description',
        'enable',
        'type_id',
        'parent_id',
        'wallet_id',
        'user_id',
    ];

    protected $hidden = [
    ];
}

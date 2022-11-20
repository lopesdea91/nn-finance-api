<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceOriginTypeModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_origin_type';
    
    protected $fillable = [
        'id',
        'description',
    ];

    protected $hidden = [
    ];
}

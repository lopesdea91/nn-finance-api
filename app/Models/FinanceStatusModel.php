<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceStatusModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_status';
    
    protected $fillable = [
        'id',
        'description',
    ];

    protected $hidden = [
    ];
}

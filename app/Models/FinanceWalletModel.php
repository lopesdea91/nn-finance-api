<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceWalletModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_wallet';

    protected $fillable = [
        'description',
        'json',
        'enable',
        'panel',
        'user_id',
    ];

    protected $hidden = [];
}

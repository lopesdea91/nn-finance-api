<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidateMonthModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_wallet_consolidate_month';

    protected $fillable = [
        'year',
        'month',
        'balance',
        'group',
        'category',
        'origin',
        'invoice',
        'wallet_id',
    ];

    protected $hidden = [];
}

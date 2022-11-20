<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceInvoiceModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_invoice';

    protected $fillable = [
        'id',
        'total',
        'date',
        'enable',
        'origin_id',
        'status_id',
        'wallet_id',
        'user_id',
        'item_id',
    ];

    protected $hidden = [];
}

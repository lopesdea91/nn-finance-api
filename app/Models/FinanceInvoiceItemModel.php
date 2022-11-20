<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceInvoiceItemModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_invoice_item';

    protected $fillable = [
        'id',
        'value',
        'obs',
        'sort',
        'type_id',
        'group_id',
        'category_id',
        'invoice_id',
    ];

    protected $hidden = [];
}

<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceListModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_list';

    protected $fillable = [
        'id',
        'total',
        'date',
        'origin_id',
        'status_id',
        'type',
        'type_id',
        'invoice_id',
        'wallet_id',
        'user_id',
    ];

    protected $hidden = [];

    public $timestamps = false;
}

<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceGroupModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_group';

    protected $fillable = [
        'id',
        'description',
        'enable',
        'type_id',
        'wallet_id',
        'user_id',
    ];

    protected $hidden = [];
}

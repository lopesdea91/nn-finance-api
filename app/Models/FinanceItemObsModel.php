<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceItemObsModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_item_obs';

    protected $fillable = [
        "id",
        "obs",
        "item_id",
    ];

    protected $hidden = [];

    public $timestamps = false;
}

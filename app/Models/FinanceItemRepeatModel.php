<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceItemRepeatModel extends Model
{
  // use HasFactory;

  protected $table = 'finance_item_repeat';

  protected $fillable = [
    'item_id',
    'repeat',
  ];

  protected $hidden = [];
}

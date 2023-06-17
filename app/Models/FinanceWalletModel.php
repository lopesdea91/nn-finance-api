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
        'enable',
        'panel',
        'user_id',
    ];

    protected $hidden = [
        'user_id'
    ];

    public function user()
    {
        return $this->hasOne("App\Models\UserModel", 'id', 'user_id');
    }
}

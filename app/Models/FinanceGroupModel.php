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
        'type_id',
        'wallet_id',
        'user_id'
    ];

    protected $hidden = [];

    public function type()
    {
        return $this->hasOne("App\Models\FinanceTypeModel", 'id', 'type_id');
    }

    public function wallet()
    {
        return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
    }
}

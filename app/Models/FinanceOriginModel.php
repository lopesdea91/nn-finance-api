<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceOriginModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_origin';

    protected $fillable = [
        'id',
        'description',
        'enable',
        'type_id',
        'parent_id',
        'wallet_id',
    ];

    protected $hidden = [];

    public function type()
    {
        return $this->hasOne("App\Models\FinanceOriginTypeModel", 'id', 'type_id');
    }

    public function parent()
    {
        return $this->hasOne("App\Models\FinanceOriginModel", 'id', 'parent_id');
    }

    public function wallet()
    {
        return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
    }
}

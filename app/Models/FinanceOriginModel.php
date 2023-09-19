<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceOriginModel extends Model
{
    // use HasFactory;
    use SoftDeletes;

    protected $table = 'finance_origin';

    protected $fillable = [
        'id',
        'description',
        'type_id',
        'parent_id',
        'wallet_id',
    ];

    protected $hidden = [];

    public $timestamps = false;

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

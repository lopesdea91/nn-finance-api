<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceWalletModel extends Model
{
    use SoftDeletes;

    protected $table = 'finance_wallet';

    protected $fillable = [
        'description',
        'panel',
        'user_id',
    ];

    protected $hidden = [
        'user_id'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne("App\Models\UserModel", 'id', 'user_id');
    }
}

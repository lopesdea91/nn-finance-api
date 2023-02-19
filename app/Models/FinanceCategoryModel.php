<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceCategoryModel extends Model
{
    // use HasFactory;

    protected $table = 'finance_category';

    protected $fillable = [
        'id',
        'description',
        'enable',
        'obs',
        'group_id',
        'wallet_id',
        'user_id',
    ];

    protected $hidden = [];
    
	public function group()
	{
		return $this->hasOne("App\Models\FinanceGroupModel", 'id', 'group_id');
	}

	public function wallet()
	{
		return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
	}

	public function closure()
	{
		return $this->hasOne("App\Models\FinanceCategoryClosureModel", 'category_id', 'id');
	}
}

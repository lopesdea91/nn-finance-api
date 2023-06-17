<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidationBalanceModel extends Model
{
	protected $table = 'finance_wallet_consolidation_balance';

	protected $fillable = [
		'revenue',
		'expense',
		'available',
		'estimate',
	];

	protected $hidden = [];

	public $timestamps = false;

	public function balance()
	{
		return $this->hasOne("App\Models\FinanceWalletConsolidationBalanceModel", 'consolidation_id', 'id');
	}
}

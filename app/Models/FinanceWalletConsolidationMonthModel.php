<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidationMonthModel extends Model
{
	protected $table = 'finance_wallet_consolidation_month';

	protected $fillable = [
		'year',
		'month',
		'wallet_id',
	];

	protected $hidden = [];

	public $timestamps = false;

	public function balance()
	{
		return $this->hasOne("App\Models\FinanceWalletConsolidationBalanceModel", 'consolidation_id', 'id')
			->select('revenue', 'expense', 'available', 'estimate', 'consolidation_id');
	}

	public function composition()
	{
		return $this->hasMany("App\Models\FinanceWalletConsolidationCompositionModel", 'consolidation_id', 'id')
			->select('id', 'value_current', 'value_limit', 'percentage_limit', 'percentage_current', 'tag_id', 'consolidation_id')
			->with('tag:id,description');
	}

	public function originTransactional()
	{
		return $this->hasMany("App\Models\FinanceWalletConsolidationOriginModel", 'consolidation_id', 'id')
			->select('id', 'sum', 'revenue', 'expense', 'average', 'origin_id', 'consolidation_id')
			->with('origin:id,description')
			->whereHas('origin', function ($q) {
				$q->where('type_id', '=', 1);
			});
	}

	public function originCredit()
	{
		return $this->hasMany("App\Models\FinanceWalletConsolidationOriginModel", 'consolidation_id', 'id')
			->select('id', 'sum', 'origin_id', 'consolidation_id')
			->with('origin:id,description')
			->whereHas('origin', function ($q) {
				$q->where('type_id', '=', 2);
			});
	}
}

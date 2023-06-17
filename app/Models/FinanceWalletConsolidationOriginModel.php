<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidationOriginModel extends Model
{
	protected $table = 'finance_wallet_consolidation_origin';

	protected $fillable = [
		'sum',
		'revenue',
		'expense',
		'average',
		'origin_id',
		'consolidation_id',
	];

	protected $hidden = [];

	public $timestamps = false;

	public function origin()
	{
		return $this->hasOne("App\Models\FinanceOriginModel", 'id', 'origin_id');
	}
}

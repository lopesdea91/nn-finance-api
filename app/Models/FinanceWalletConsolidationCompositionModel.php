<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidationCompositionModel extends Model
{
	protected $table = 'finance_wallet_consolidation_composition';

	protected $fillable = [
		'value_current',
		'value_limit',
		'percentage_limit',
		'percentage_current',
		'tag_id',
		'consolidation_id',
	];

	protected $hidden = [];

	public $timestamps = false;

	public function tag()
	{
		return $this->hasOne("App\Models\FinanceTagModel", 'id', 'tag_id');
	}
}

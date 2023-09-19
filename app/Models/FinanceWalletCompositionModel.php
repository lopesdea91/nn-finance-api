<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletCompositionModel extends Model
{
	protected $table = 'finance_wallet_composition';

	protected $fillable = [
		'percentage_limit',
		'tag_id',
		'wallet_id'
	];

	protected $hidden = [];

	public $timestamps = false;

	public function tag()
	{
		return $this->hasOne("App\Models\FinanceTagModel", 'id', 'tag_id');
	}

	public function wallet()
	{
		return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
	}
}

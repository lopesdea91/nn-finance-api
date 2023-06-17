<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidationTagModel extends Model
{
	protected $table = 'finance_wallet_consolidation_tag';

	protected $fillable = [
		'sum',
		'type_id',
		'consolidation_id',
	];

	protected $hidden = [];

	public $timestamps = false;

	public function tags()
	{
		return $this->belongsToMany("App\Models\FinanceWalletConsolidationTagsModel", 'finance_wallet_consolidation_tags', 'consolidation_tag_id', 'tag_id');
	}
}

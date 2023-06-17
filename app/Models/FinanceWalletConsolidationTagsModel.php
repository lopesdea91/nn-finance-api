<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceWalletConsolidationTagsModel extends Model
{
	protected $table = 'finance_wallet_consolidation_tags';

	protected $fillable = [
		'id',
		'tag_id',
		'consolidation_tag_id',
	];

	protected $hidden = [];

	public $timestamps = false;
}

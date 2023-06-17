<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceItemTagModel extends Model
{
	protected $table = 'finance_item_tag';

	protected $fillable = [
		'id',
		'item_id',
		'tag_id',
	];

	protected $hidden = [];
}

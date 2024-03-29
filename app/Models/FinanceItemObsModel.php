<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceItemObsModel extends Model
{
	protected $table = 'finance_item_obs';

	protected $fillable = [
		"id",
		"obs",
		"item_id",
	];

	protected $hidden = [];

	public $timestamps = false;
}

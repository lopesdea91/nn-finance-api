<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTagModel extends Model
{
	// use HasFactory;
	use SoftDeletes;

	protected $table = 'finance_tag';

	protected $fillable = [
		"id",
		"description",
		"type_id",
		"wallet_id",
		"created_at",
		"updated_at",
	];

	protected $hidden = [];

	public $timestamps = false;

	public function type()
	{
		return $this->hasOne("App\Models\FinanceTypeModel", 'id', 'type_id');
	}

	public function wallet()
	{
		return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
	}
}

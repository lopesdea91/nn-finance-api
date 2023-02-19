<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceTagModel extends Model
{
	protected $table = 'finance_tag';

	protected $fillable = [
		"id",
		"description",
		"enable",
		"type_id",
		"wallet_id",
		"created_at",
		"updated_at",
	];

	protected $hidden = [];

	public function type()
	{
		return $this->hasOne("App\Models\FinanceOriginTypeModel", 'id', 'type_id');
	}

	public function wallet()
	{
		return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
	}
}

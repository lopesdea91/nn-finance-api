<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

// use App\Casts\FinanceItemTagsCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceItemModel extends Model
{
	// use HasFactory;
	use SoftDeletes;

	protected $table = 'finance_item';

	protected $fillable = [
		'id',
		'value',
		'date',
		'sort',
		'balance',
		'origin_id',
		'status_id',
		'type_id',
		'wallet_id',
		'deleted_at',
	];

	protected $hidden = [];

	public function wallet()
	{
		return $this->hasOne("App\Models\FinanceWalletModel", 'id', 'wallet_id');
	}
	public function origin()
	{
		return $this->hasOne("App\Models\FinanceOriginModel", 'id', 'origin_id');
	}
	public function type()
	{
		return $this->hasOne("App\Models\FinanceTypeModel", 'id', 'type_id');
	}
	public function status()
	{
		return $this->hasOne("App\Models\FinanceStatusModel", 'id', 'status_id');
	}
	public function obs()
	{
		return $this->hasOne("App\Models\FinanceItemObsModel", 'item_id', 'id');
	}
	public function tags()
	{
		return $this->belongsToMany("App\Models\FinanceTagModel", 'finance_item_tag', 'item_id', 'tag_id')->orderBy('finance_item_tag.id');
	}
}

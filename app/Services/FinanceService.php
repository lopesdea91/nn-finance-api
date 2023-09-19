<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Finance\{
	Origin\FinanceOriginCollection,
	OriginType\FinanceOriginTypeCollection,
	Status\FinanceStatusCollection,
	Tag\FinanceTagCollection,
	Type\FinanceTypeCollection,
	Wallet\FinanceWalletCollection,
	Wallet\FinanceWalletResource,
};
use App\Models\FinanceOriginModel;
use App\Models\FinanceOriginTypeModel;
use App\Models\FinanceStatusModel;
use App\Models\FinanceTagModel;
use App\Models\FinanceTypeModel;
use App\Models\FinanceWalletModel;

class FinanceService
{
	static function data()
	{
		$user = Auth::user();
		$user_id = $user->id;

		$typeAll = FinanceTypeModel::select('id', 'description')->get();

		$statusAll = FinanceStatusModel::select('id', 'description')->get();

		$originTypeAll = FinanceOriginTypeModel::select('id', 'description')->orderBy('description')->get();

		$walletPanel = FinanceWalletModel::select(
			'description',
			'panel',
			'user_id',
		)->where([
			'user_id' =>  $user_id,
			'panel'		=> 1
		])
			->first();

		$wallet = FinanceWalletModel::select(
			'id',
			'description',
			'panel',
			'user_id',
		)->where([
			'user_id' =>  $user_id
		])
			->orderBy('description')
			->get();

		$origin = FinanceOriginModel::select(
			'id',
			'description',
			'type_id',
			'parent_id',
			'wallet_id',
			"wallet_id",
		)
			->whereHas('wallet', function ($q) use ($user_id) {
				$q->where('user_id', '=', $user_id);
			})
			->orderBy('description')
			->get();

		$tag = FinanceTagModel::select(
			"id",
			"description",
			"type_id",
			"wallet_id",
			'deleted_at'
		)
			->whereHas('wallet', function ($q) use ($user_id) {
				$q->where('user_id', $user_id);
			})
			->orderBy('description')
			->get();

		$data = [];
		// $data['user']['id'] 		= $user->id;
		// $data['user']['name'] 	= $user->name;
		// $data['user']['email']	= $user->email;

		$data['wallet_panel'] 	= $walletPanel->count() ? new FinanceWalletResource($walletPanel->first()) : null;
		$data['wallet']       	= new FinanceWalletCollection($wallet);
		// $data['group']        = [];
		// $data['category']     = [];
		$data['origin']       	= new FinanceOriginCollection($origin);
		$data['tag']       			= new FinanceTagCollection($tag);
		$data['type']         	= new FinanceTypeCollection($typeAll);
		$data['status']       	= new FinanceStatusCollection($statusAll);
		$data['origin_type']   	= new FinanceOriginTypeCollection($originTypeAll);

		return $data;
	}
}

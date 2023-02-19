<?php

namespace App\Services;

use Carbon\Carbon;
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
use App\Repository\{
	FinanceOriginRepository,
	FinanceOriginTypeRepository,
	FinanceStatusRepository,
	FinanceTagRepository,
	FinanceTypeRepository,
	FinanceWalletRepository,
};

class FinanceService
{
	static function data()
	{
		$user = Auth::user();
		$user_id = $user->id;

		$typeAll      	= (new FinanceTypeRepository)->all()->get();

		$statusAll      = (new FinanceStatusRepository)->all()->get();

		$originTypeAll  = (new FinanceOriginTypeRepository)->all()->orderBy('description')->get();

		$walletPanel    = (new FinanceWalletRepository)->all([
			"where" => [
				'user_id' =>  $user_id,
				'panel'		=> 1
			]
		])->get();

		$wallet         = (new FinanceWalletRepository)->all([
			"where" => [
				'user_id' =>  $user_id
			]
		])->orderBy('description')->get();

		$origin         = (new FinanceOriginRepository)->all([
			"whereHas" => [
				'wallet'     => function ($q) use ($user_id) {
					$q->where('user_id', $user_id);
				}
			]
		])->orderBy('description')->get();

		$tag 						= (new FinanceTagRepository)->all([
			"whereHas" => [
				'wallet'     => function ($q) use ($user_id) {
					$q->where('user_id', $user_id);
				}
			]
		])->orderBy('description')->get();

		$data = [];
		$data['user']['id'] 		= $user->id;
		$data['user']['name'] 	= $user->name;
		$data['user']['email']	= $user->email;

		$data['wallet_panel'] 	= $walletPanel->count() ? new FinanceWalletResource($walletPanel->first()) : null;
		$data['wallet']       	= new FinanceWalletCollection($wallet);
		// $data['group']        = [];
		// $data['category']     = [];
		$data['origin']       	= new FinanceOriginCollection($origin);
		$data['tag']       			= new FinanceTagCollection($tag);
		$data['type']         	= new FinanceTypeCollection($typeAll);
		$data['status']       	= new FinanceStatusCollection($statusAll);
		$data['originType']   	= new FinanceOriginTypeCollection($originTypeAll);

		return $data;
	}
}

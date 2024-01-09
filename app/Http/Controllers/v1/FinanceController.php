<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Finance\Origin\FinanceOriginListResource;
use App\Http\Resources\Finance\OriginType\FinanceOriginTypeListResource;
use App\Http\Resources\Finance\Status\FinanceStatusListResource;
use App\Http\Resources\Finance\Tag\FinanceTagListResource;
use App\Http\Resources\Finance\Type\FinanceTypeListResource;
use App\Http\Resources\Finance\Wallet\FinanceWalletResource;
use App\Repositories\FinanceOriginRepository;
use App\Repositories\FinanceOriginTypeRepository;
use App\Repositories\FinanceStatusRepository;
use App\Repositories\FinanceTagRepository;
use App\Repositories\FinanceTypeRepository;
use App\Repositories\FinanceWalletRepository;
use Illuminate\Support\Facades\Auth;

class FinanceController
{
	private $user_id;

	public function __construct(
		private FinanceTypeRepository $financeTypeRepository,
		private FinanceStatusRepository $financeStatusRepository,
		private FinanceOriginTypeRepository $financeOriginTypeRepository,
		private FinanceWalletRepository $financeWalletRepository,
		private FinanceOriginRepository $financeOriginRepository,
		private FinanceTagRepository $financeTagRepository,
	) {
		$user = Auth::user();
		$this->user_id = $user->id;
	}

	public function data()
	{
		return [
			'type'         	=> $this->getType(),
			'status' 				=> $this->getStatus(),
			'originType' 		=> $this->getOriginType(),
			'wallet_panel' 	=> $this->getWalletPanel(),
			'wallet'       	=> $this->getWallet(),
			'origin' 				=> $this->getOrigin(),
			'tag' 					=> $this->gatTag(),
		];
	}

	private function getType()
	{
		return FinanceTypeListResource::collection($this->financeTypeRepository->get());
	}
	private function getStatus()
	{
		return FinanceStatusListResource::collection($this->financeStatusRepository->get());
	}
	private function getOriginType()
	{
		return FinanceOriginTypeListResource::collection($this->financeOriginTypeRepository->get());
	}
	private function getWalletPanel()
	{
		$walletPanel = $this->financeWalletRepository->query([
			'user_id' =>  $this->user_id,
			'panel'		=> 1
		])->select(
			'description',
			'panel',
			'user_id',
		)->first();

		return $walletPanel->count() ? new FinanceWalletResource($walletPanel->first()) : null;
	}
	private function getWallet()
	{
		$wallet = $this->financeWalletRepository->query([
			'user_id' =>  $this->user_id,
		])->select(
			'id',
			'description',
			'panel'
		)->orderBy('description')
			->get();

		return FinanceWalletResource::collection($wallet);
	}
	private function getOrigin()
	{
		return FinanceOriginListResource::collection($this->financeOriginRepository->get([
			'user_id' =>  $this->user_id,
		]));
	}
	private function gatTag()
	{
		return FinanceTagListResource::collection($this->financeTagRepository->get([
			'user_id' =>  $this->user_id,
		]));
	}
}

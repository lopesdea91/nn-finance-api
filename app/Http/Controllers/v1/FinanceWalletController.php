<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\FinanceWalletConsolidateMonthService;
use App\Services\FinanceWalletService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceWalletController extends CrudController
{
	protected $service;
	protected $nameSingle = 'wallet';
	protected $nameMultiple = 'wallets';
	protected $resource = 'App\Http\Resources\Finance\Wallet\FinanceWalletResource';
	protected $collection = 'App\Http\Resources\Finance\Wallet\FinanceWalletCollection';
	protected $validateStore = [
		'description' => 'required|unique:finance_wallet,description',
	];
	protected $fieldsStore = [
		'description'
	];
	protected $validateUpdate = [
		'description'   => 'required|string',
		'json'          => 'nullable|string',
		'enable'        => 'required|integer',
		'panel'         => 'required|integer',
	];
	protected $fieldsUpdate = [
		'description',
		'json',
		'enable',
		'panel',
	];
	protected $walletConsolidateMonthService;

	public function __construct()
	{
		$this->service = new FinanceWalletService;
		$this->walletConsolidateMonthService = new FinanceWalletConsolidateMonthService;
	}

	public function consolidateMonth(Request $request)
	{
		$request->validate([
			'period'   	=> 'required|string',
			'wallet_id' => 'required|integer',
		]);

		try {
			$fields = $request->only(['period', 'wallet_id']);

			$explode_period = explode('-', $fields['period']);

			$args = [
				'where' => [
					'year'			=> $explode_period[0],
					'month'			=> $explode_period[1],
					'wallet_id' => $fields['wallet_id'],
				],
			];

			$consolidate_base = $this->walletConsolidateMonthService->data_consolidate_base;

			$results = $this->walletConsolidateMonthService->query($args)->get();
			$hasContent = $results->count();

			if ($hasContent) {
				$content = $results->first();

				$consolidate_base["balance"] = json_decode($content['balance']);
				$consolidate_base["status"]  = json_decode($content['status']);
				$consolidate_base["tag"]     = json_decode($content['tag']);
				$consolidate_base["origin"]  = json_decode($content['origin']);
				$consolidate_base["invoice"] = json_decode($content['invoice']);
			}

			$rtn = [
				// "year"      => $consolidate_base['year'],
				// "month"     => $consolidate_base['month'],
				// "wallet_id" => $consolidate_base['wallet_id'],
				"balance"   => $consolidate_base['balance'],
				"status"    => $consolidate_base['status'],
				"tag"       => $consolidate_base['tag'],
				"origin"    => $consolidate_base['origin'],
				"invoice"   => $consolidate_base['invoice'],
			];

			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function processConsolidateMonth(Request $request)
	{
		$request->validate([
			'period'   	=> 'required|string',
			'wallet_id' => 'required|integer',
		]);

		$fields = $request->only(['period', 'wallet_id']);

		try {
			$this->walletConsolidateMonthService->consolidate($fields);

			$rtn = ['message' => 'OK'];
			// $rtn = $data;
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function periodsData(Request $request)
	{
		$request->validate([
			'period'   	=> 'required|string',
			'wallet_id' => 'required|integer',
			'format' 		=> 'string',
		]);

		$fields = $request->only(['period', 'wallet_id', 'format']);

		try {
			$rtn = [
				'items' => $this->service->periodsData($fields)
			];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

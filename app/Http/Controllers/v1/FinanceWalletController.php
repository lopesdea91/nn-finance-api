<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Wallet\FinanceWalletResource;
use App\Models\FinanceItemModel;
use App\Models\FinanceWalletCompositionModel;
use App\Models\FinanceWalletModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FinanceWalletController
{
	private $nameSingle = 'wallet';
	private $nameMultiple = 'wallets';

	public function all(Request $request)
	{
		try {
			$query = $request->all();

			$params = [
				'where' => [
					'user_id'	=> Auth::user()->id
				],
				'page' => [],
			];
			# WHERE 
			if (key_exists('_q',          $query))  $params['where'][] = ['description', 'like', "%{$query['_q']}%"];
			if (key_exists('enable',      $query))  $params['where'][] = ['enable',      '=',    $query['enable']];
			if (key_exists('panel',      	$query))  $params['where'][] = ['panel',       '=',    $query['panel']];
			if (key_exists('_sort',   		$query))  $params['page']['_sort'] 	= $query['_sort'];
			if (key_exists('_order',   		$query))  $params['page']['_order'] = $query['_order'];
			if (key_exists('_limit',   		$query))  $params['page']['_limit'] = $query['_limit'];

			$hasPaginate = key_exists('_paginate', $query);

			$result = $hasPaginate
				? $this->page($params)
				: $this->get($params);

			if ($result['count']) {
				$rtn = $result['data'];
				$sts = Response::HTTP_OK;
			} else {
				$rtn = null;
				$sts = Response::HTTP_NO_CONTENT;
			}
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function page($params)
	{
		$where = $params['where'];
		$page  = $params['page'];

		$order  = 'id';
		$sort   = 'desc';
		$limit 	= 15;

		# ORDER 
		if (key_exists('_order', $page)) {
			$order = $page['_order'];

			if ($order === 'updated') $order = 'updated_at';
			if ($order === 'created') $order = 'created_at';
		}
		# SORT
		if (key_exists('_sort', $page)) {
			$sort = $page['_sort'];

			if (in_array($sort, ['asc', 'desc'])) {
				$order = "{$order} $sort";
			}
		}
		# LIMIT
		if (key_exists('_limit', $page)) {
			$limit = $page['_limit'];
		}

		$result = FinanceWalletModel::select(
			'id',
			'description',
			'enable',
			'panel',
			'user_id',
		)
			->where($where)
			->orderByRaw($order)
			->paginate($limit);

		return [
			'count' => $result->count(),
			'data' => [
				"items"     => FinanceWalletResource::collection($result->items()),
				"page"      => $result->currentPage(),
				"total"     => $result->total(),
				"limit"     => $result->perPage(),
				"lastPage"  => $result->lastPage(),
			],
		];
	}
	public function get($params)
	{
		$where = $params['where'];

		$result = FinanceWalletModel::select(
			'id',
			'description',
			'enable',
			'panel',
			'user_id',
		)
			->where($where);

		return [
			'count' => $result->count(),
			'data' => FinanceWalletResource::collection($result->get()),
		];
	}
	public function id($id)
	{
		try {
			$result = FinanceWalletModel::select(
				'description',
				'enable',
				'panel',
				"created_at",
				"updated_at"
			)
				// ->with(
				// 	'type',
				// 	'wallet'
				// )
				->find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$rtn = new FinanceWalletResource($result);
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function store(Request $request)
	{
		try {
			$request->validate([
				'description'   => 'required|string',
			]);

			$fields = $request->only([
				'description',
			]);

			FinanceWalletModel::create([
				'description' => $fields['description'],
				'enable' 			=> 1,
				'panel' 			=> 1,
				'user_id' 		=> Auth::user()->id,
			]);

			$rtn = ['message' => "{$this->nameSingle} cadastrada"];
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function update($id, Request $request)
	{
		try {
			$result = FinanceWalletModel::find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$request->validate([
				'description'   => 'required|string',
				'enable'       	=> 'nullable|integer',
				'panel'       	=> 'nullable|integer',
			]);

			$fields = $request->only([
				'description',
				'enable',
				'panel'
			]);

			$result->update([
				'description' => $fields['description'],
				'enable' 			=> $fields['enable'],
				'panel' 			=> $fields['panel'],
			]);

			$rtn = ['message' => "{$this->nameSingle} atualizada"];
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function delete($id)
	{
		$result = FinanceWalletModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$rtn = $result->delete();
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function enabled($id)
	{
		$result = FinanceWalletModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$result->update(['enable' => '1']);

			$rtn = ['message' => "{$this->nameSingle} ativa"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json([], $sts);
	}
	public function disabled($id)
	{
		$result = FinanceWalletModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$result->update(['enable' => '0']);

			$rtn = ['message' => "{$this->nameSingle} inativa"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function dataPeriods(Request $request)
	{
		try {
			$request->validate([
				'wallet_id' => 'required|integer',
				'format' 		=> 'string',
			]);

			$fields = $request->only(['period', 'wallet_id', 'format']);

			$items = FinanceItemModel::where([
				'wallet_id' => $fields['wallet_id'],
				'enable'    => 1,
			])
				->selectRaw(DB::raw("DATE_FORMAT(date, '%Y-%m') as period"))
				->groupBy(DB::raw('period'))
				->get()
				->map(function ($value) {
					$ex_period = explode('-', $value->period);
					$current_now = now()->setDate($ex_period[0], $ex_period[1], 01);

					$value->year = $current_now->format('Y');
					$value->month = $current_now->format('m');
					$value->label = $current_now->format('m-Y');

					return $value;
				})
				->toArray();

			$rtn = $items;
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function composition($id, Request $request)
	{
		$request->validate([
			'composition' => 'required',
		]);

		$fields = $request->only(['composition']);

		try {
			$fields = $request->only(['composition']);

			$composition = $fields['composition'];

			// delete old 
			FinanceWalletCompositionModel::where([
				'wallet_id' => $id,
			])->delete();

			// create new
			foreach ($composition as $value) {
				FinanceWalletCompositionModel::create([
					'percentage_limit' => $value['percentage_limit'],
					'tag_id' => $value['tag_id'],
					'wallet_id' => $id,
				]);
			}

			$rtn = ['message' => "Composição atualizada!"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

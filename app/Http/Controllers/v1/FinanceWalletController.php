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
			if (key_exists('panel',      	$query))  $params['where'][] = ['panel',       '=',    $query['panel']];
			if (key_exists('deleted',			$query))  $params['where']['deleted']	= $query['deleted'];

			if (key_exists('_sort',   		$query))  $params['page']['_sort'] 		= $query['_sort'];
			if (key_exists('_order',   		$query))  $params['page']['_order'] 	= $query['_order'];
			if (key_exists('_limit',   		$query))  $params['page']['_limit'] 	= $query['_limit'];

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
		$page  			= $params['page'];
		$where 			= $params['where'];
		$onlyTrashed = key_exists('deleted', $params['where']);

		unset($where['deleted']);
		unset($params['where']['deleted']);

		$order  = 'id';
		$sort   = 'desc';
		$limit 	= 15;

		# ORDER 
		if (key_exists('_order', $page)) {
			$order = $page['_order'];
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

		$columns = [
			'id',
			'description',
			'panel',
			'user_id',
			'deleted_at',
		];

		$model = $onlyTrashed
			? FinanceWalletModel::onlyTrashed()->select($columns)
			: FinanceWalletModel::withTrashed(false)->select($columns);

		$result = $model->where($where)->orderByRaw($order)->paginate($limit);

		return [
			'count' => count($result->items()),
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
		$onlyTrashed = key_exists('deleted', $params['where']);

		unset($where['deleted']);
		unset($params['where']['deleted']);

		$columns = [
			'id',
			'description',
			'panel',
			'user_id',
			'deleted_at',
		];

		$model = $onlyTrashed
			? FinanceWalletModel::onlyTrashed()->select($columns)
			: FinanceWalletModel::withTrashed(false)->select($columns);

		$model->where($where);

		return [
			'count' => $model->count(),
			'data' => FinanceWalletResource::collection($model->get()),
		];
	}
	public function id($id)
	{
		try {
			$result = FinanceWalletModel::withTrashed()->select(
				'id',
				'description',
				'panel',
				"deleted_at"
			)->find($id);

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
				'panel'       	=> 'nullable|integer',
			]);

			$fields = $request->only([
				'description',
				'panel'
			]);

			$result->update([
				'description' => $fields['description'],
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
		$result = FinanceWalletModel::withTrashed()->find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			if (!!$result->deleted_at) {
				/** DELETE TAG */
				/** DELETE finance_wallet_consolidation_tags */
				/** DELETE finance_wallet_consolidation_tag */
				/** DELETE finance_wallet_consolidation_origin */
				/** DELETE finance_wallet_consolidation_month */
				/** DELETE finance_wallet_consolidation_composition */
				/** DELETE finance_wallet_consolidation_balance */
				/** DELETE finance_wallet_composition */
				/** DELETE finance_wallet */
				/** DELETE finance_item_repat */
				/** DELETE finance_item_obs */
				/** DELETE finance_item_tag */
				/** DELETE finance_item */
				/** DELETE finance_list_item */
				/** DELETE finance_list */
				/** DELETE finance_invoice_item */
				/** DELETE finance_invoice */

				$result->forceDelete();
			} else {
				$result->delete();
			}

			$rtn = $result->delete();
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function restore($id)
	{
		$result = FinanceWalletModel::onlyTrashed()->find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não esta deletado para restaurar!");

		try {
			$result->restore();

			$rtn = ['message' => "{$this->nameSingle} restaurada!"];
			$sts = Response::HTTP_OK;
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

	public function getComposition(Request $request)
	{
		$user_id	= Auth::user()->id;

		$request->validate([
			'wallet_id' => 'required',
		]);

		try {
			$fields = $request->only(['wallet_id']);

			$wallet_id = $fields['wallet_id'];

			$result = FinanceWalletCompositionModel::where([
				'wallet_id' => $wallet_id,
			])
				->whereHas('wallet', function ($q) use ($user_id) {
					$q->where('user_id', '=', $user_id);
				})
				->get();

			$rtn = $result;
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function createComposition(Request $request)
	{
		$user_id	= Auth::user()->id;

		$request->validate([
			'wallet_id' => 'required',
			'composition' => 'required',
		]);

		try {
			$fields = $request->only(['wallet_id', 'composition']);

			$composition = $fields['composition'];
			$wallet_id = $fields['wallet_id'];


			// delete old 
			FinanceWalletCompositionModel::where([
				'wallet_id' => $wallet_id,
			])
				->whereHas('wallet', function ($q) use ($user_id) {
					$q->where('user_id', '=', $user_id);
				})
				->delete();

			// create new
			foreach ($composition as $value) {
				FinanceWalletCompositionModel::create([
					'percentage_limit' => $value['percentage_limit'],
					'tag_id' => $value['tag_id'],
					'wallet_id' => $wallet_id,
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
	public function deleteComposition($id, Request $request)
	{
		$user_id	= Auth::user()->id;

		$request->validate([
			'wallet_id' => 'required|exists:finance_wallet,id',
		]);

		$fields = $request->only([
			'wallet_id',
		]);

		$fields = $request->only(['wallet_id']);

		$wallet_id = $fields['wallet_id'];

		$result = FinanceWalletCompositionModel::where([
			'wallet_id' => $wallet_id,
		])
			->whereHas('wallet', function ($q) use ($user_id) {
				$q->where('user_id', '=', $user_id);
			})
			->find($id);

		if (!$result)
			throw new ApiExceptionResponse("consolidation: id ($id) não existe!");

		try {
			$rtn = $result->delete();
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

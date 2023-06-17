<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Item\FinanceItemResource;
use App\Models\FinanceItemModel;
use App\Models\FinanceItemObsModel;
use App\Models\FinanceItemTagModel;
use App\Services\FinanceWalletConsolidationMonthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FinanceItemController
{
	private $nameSingle = 'item';
	private $nameMultiple = 'items';

	public function all(Request $request)
	{
		try {
			$query = $request->all();

			$params = [
				'where' => [
					'tag_ids' => [],
				],
				'page' => [],
			];
			# WHERE 
			if (key_exists('_q',          $query))  $params['where'][] = ['description', 'like', "%{$query['_q']}%"];

			if (key_exists('enable',      	$query))  $params['where'][] = ['enable',       '=',    $query['enable']];
			if (key_exists('origin_id', 		$query))  $params['where'][] = ['origin_id',    '=',    $query['origin_id']];
			if (key_exists('status_id', 		$query))  $params['where'][] = ['status_id',    '=',    $query['status_id']];
			if (key_exists('type_id',     	$query))  $params['where'][] = ['type_id',      '=',    $query['type_id']];
			if (key_exists('wallet_id',   	$query))  $params['where'][] = ['wallet_id',    '=',    $query['wallet_id']];
			if (key_exists('tag_ids',				$query))  $params['where']['tag_ids'] 				= $query['tag_ids'];
			if (key_exists('period',				$query))  $params['where']['period'] 				= $query['period'];
			if (key_exists('type_preview',	$query))  $params['where']['type_preview'] 	= $query['type_preview'];

			if (key_exists('_sort',   		  $query))  $params['page']['_sort'] 	= $query['_sort'];
			if (key_exists('_order',   		  $query))  $params['page']['_order'] = $query['_order'];
			if (key_exists('_limit',   		  $query))  $params['page']['_limit'] = $query['_limit'];

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

		if (!key_exists('type_preview', $where))
			throw new ApiExceptionResponse("type_preview é um campo obrigatório!");

		if (!key_exists('period', $where))
			throw new ApiExceptionResponse("period é um campo obrigatório!");

		$order  = 'date';
		$sort   = 'desc';
		$limit 	= 15;

		$ex_period = explode('-', $where['period']);
		$current_period = now()->setDate($ex_period[0], $ex_period[1], 01);

		$whereYear   = null;
		$whereMonth  = null;


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

		# type_preview=extract
		if (key_exists('type_preview', $where)) {
			$type_preview = $where['type_preview'];

			if ($type_preview == 'extract') {
				$order = 'date';
				$sort  = 'desc';

				$whereYear   = $current_period->format('Y');
				$whereMonth  = $current_period->format('m');
			}
			if ($type_preview == 'historic') {
				$order = 'id';
				$sort  = 'desc';
			}
			if ($type_preview == 'moviment') {
				$order = 'date';
				$sort  = 'desc';
			}

			$order = "{$order} $sort";
		}

		unset($where['period']);
		unset($where['type_preview']);

		$model = FinanceItemModel::select(
			'id',
			'value',
			'date',
			'sort',
			'enable',
			'origin_id',
			'status_id',
			'type_id',
			'wallet_id',
		)
			->with([
				'type',
				'status',
				'origin',
				'obs',
				'tags',
				'wallet' => function ($q) {
					$q->where('user_id', Auth::user()->id);
				}
			])->where($where);


		if ($whereYear) {
			/** date contains -> Y */
			$model->whereYear('date',  $whereYear);
		}
		if ($whereMonth) {
			/** date contains -> M */
			$model->whereMonth('date', $whereMonth);
		}

		$result = $model->orderByRaw($order)->paginate($limit);

		return [
			'count' => $result->count(),
			'data' => [
				"items"     => FinanceItemResource::collection($result->items()),
				"page"      => $result->currentPage(),
				"total"     => $result->total(),
				"limit"     => $result->perPage(),
				"lastPage"  => $result->lastPage(),
			],
		];
	}
	public function get($params)
	{
		$where 	 = $params['where'];
		$tag_ids = $where['tag_ids'];

		unset($where['tag_ids']);

		$result = FinanceItemModel::select(
			'id',
			'value',
			'date',
			'sort',
			'enable',
			'origin_id',
			'status_id',
			'type_id',
			'wallet_id',
		)
			->with([
				'type',
				'status',
				'origin',
				'obs',
				'tags' => function ($q) use ($tag_ids) {
					if (!!$tag_ids) {
						$q->whereIn('tag_id', $tag_ids);
					}
				},
				'wallet' => function ($q) {
					$q->where('user_id', Auth::user()->id);
				}
			])
			->where($where);

		return [
			'count' => $result->count(),
			'data' => FinanceItemResource::collection($result->get()),
		];
	}
	public function id($id)
	{
		try {
			$result = FinanceItemModel::select(
				'id',
				'value',
				'date',
				'sort',
				'enable',
				'origin_id',
				'status_id',
				'type_id',
				'wallet_id',
				"created_at",
				"updated_at"
			)
				->with([
					'type',
					'status',
					'origin',
					'obs',
					'tags',
					'wallet' => function ($q) {
						$q->where('user_id', Auth::user()->id);
					}
				])
				->find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$rtn = new FinanceItemResource($result);
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function store(Request $request)
	{

		$request->validate([
			"value"       => 'required|numeric',
			"date"        => 'required|string',
			"sort"        => 'required|integer',
			"enable"      => 'required|integer',
			"repeat"      => ['required', Rule::in('UNIQUE', 'REPEAT')],
			"origin_id"   => 'required|exists:finance_origin,id',
			"status_id"   => 'required|exists:finance_status,id',
			"type_id"     => 'required|exists:finance_type,id',
			"tag_ids"    => 'required',
			"wallet_id"   => 'required|exists:finance_wallet,id',
		]);

		$fields = $request->only([
			"value",
			"date",
			"obs",
			"sort",
			"enable",
			"repeat",
			"origin_id",
			"status_id",
			"type_id",
			"tag_ids",
			"wallet_id",
		]);

		try {
			$repeatTimes = false;
			$repeatMonths = false;
			$isRepeat = $fields['repeat'] === 'REPEAT';

			/** ITEM */
			$result = FinanceItemModel::create([
				"value"     => $fields['value'],
				"date"      => $fields['date'],
				"sort"      => $fields['sort'],
				"enable"    => $fields['enable'],
				"repeat"    => 'UNIQUE',
				"origin_id" => $fields['origin_id'],
				"status_id" => $fields['status_id'],
				"type_id"   => $fields['type_id'],
				"wallet_id" => $fields['wallet_id']
			]);

			/** TAGS */
			$result->tags()->sync([]);
			$result->tags()->sync($fields['tag_ids']);

			/** OBS */
			if (key_exists('obs', $fields)) {
				$result->obs()->create(['obs' => $fields['obs'], 'item_id' => $result->id]);
			}


			if ($isRepeat) {
				if ($repeatTimes) {
					// fazer logica usando for na chave 'for_times'
				}
				if ($repeatMonths) {
					// fazer logica usando for incrementando a mês a mês até chega o mês na chave until_month
				}
			}


			$rtn = ['message' => "{$this->nameSingle} criado(a)!"];
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
	public function update($id, Request $request)
	{
		$request->validate([
			"value"       => 'required|numeric',
			"date"        => 'required|string',
			"sort"        => 'required|integer',
			"enable"      => 'required|integer',
			"repeat"      => ['required', Rule::in('UNIQUE', 'REPEAT')],
			"origin_id"   => 'required|exists:finance_origin,id',
			"status_id"   => 'required|exists:finance_status,id',
			"type_id"     => 'required|exists:finance_type,id',
			"tag_ids"    => 'required',
			"wallet_id"   => 'required|exists:finance_wallet,id',
		]);

		$fields = $request->only([
			"value",
			"date",
			"obs",
			"sort",
			"enable",
			"repeat",
			"origin_id",
			"status_id",
			"type_id",
			"tag_ids",
			"wallet_id",
		]);

		$result = FinanceItemModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			/** TAGS */
			$result->tags()->sync([]);
			$result->tags()->sync($fields['tag_ids']);

			/** OBS */
			if (key_exists('obs', $fields)) {
				!!$result->obs
					? $result->obs()->update(['obs' => $fields['obs']])
					: $result->obs()->create(['obs' => $fields['obs'], 'item_id' => $result->id]);
			}

			/** ITEM */
			$result->update([
				"value"     => $fields['value'],
				"date"      => $fields['date'],
				"sort"      => $fields['sort'],
				"enable"    => $fields['enable'],
				// "repeat"    => $fields['repeat'],
				"origin_id" => $fields['origin_id'],
				"status_id" => $fields['status_id'],
				"type_id"   => $fields['type_id'],
				"wallet_id" => $fields['wallet_id']
			]);

			$rtn = ['message' => "{$this->nameSingle} atualizado(a)!"];
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
	public function delete($id, FinanceWalletConsolidationMonthService $financeWalletConsolidationMonthService)
	{
		$result = FinanceItemModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$item_date_current = $result->date;
			$item_walletId_current = $result->wallet_id;

			/** DELETE TAG */
			FinanceItemTagModel::where(['item_id' => $id])->delete();

			/** DELETE OBS */
			FinanceItemObsModel::where(['item_id' => $id])->delete();

			/** DELETE ITEM */
			$result->delete();

			/** CONSOLIDATION MONTH BT ITEM*/
			$financeWalletConsolidationMonthService->consolidate([
				'period'    => $item_date_current,
				'wallet_id' => $item_walletId_current,
			]);

			$rtn = ['message' => "{$this->nameSingle} deletado"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function enabled($id, FinanceWalletConsolidationMonthService $financeWalletConsolidationMonthService)
	{
		$result = FinanceItemModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$result->update(['enable' => '1']);

			/** CONSOLIDATION MONTH BT ITEM*/
			$financeWalletConsolidationMonthService->consolidate([
				'period'    => $result->date,
				'wallet_id' => $result->wallet_id,
			]);

			$rtn = ['message' => "{$this->nameSingle} ativa"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json([], $sts);
	}
	public function disabled($id, FinanceWalletConsolidationMonthService $financeWalletConsolidationMonthService)
	{
		try {
			$result = FinanceItemModel::find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$result->update(['enable' => '0']);


			/** CONSOLIDATION MONTH BT ITEM*/
			$financeWalletConsolidationMonthService->consolidate([
				'period'    => $result->date,
				'wallet_id' => $result->wallet_id,
			]);

			$rtn = ['message' => "{$this->nameSingle} inativa"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function status(Request $request, FinanceWalletConsolidationMonthService $financeWalletConsolidationMonthService)
	{
		try {
			$request->validate([
				"item_id"     => 'required|exists:finance_item,id',
				"status_id"   => 'required|exists:finance_status,id',
			]);

			$fields = $request->only([
				"item_id",
				"status_id",
			]);

			$item_id 	 = $fields['item_id'];
			$status_id = $fields['status_id'];

			$result = FinanceItemModel::find($item_id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($item_id) não existe!");

			/** UPDATE ITEM */
			$result->update([
				'status_id' => $status_id
			]);

			/** CONSOLIDATION MONTH BT ITEM*/
			$financeWalletConsolidationMonthService->consolidate([
				'period'    => $result->date,
				'wallet_id' => $result->wallet_id,
			]);

			$rtn = ['message' => "{$this->nameSingle} atualizado(a)"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}










	// protected $nameSingle = 'item';
	// protected $nameMultiple = 'items';
	// protected $service;
	// protected $resource = 'App\Http\Resources\Finance\Item\FinanceItemResource';
	// protected $collection = 'App\Http\Resources\Finance\Item\FinanceItemCollection';
	// protected $validateStore = [
	// 	'description' => 'required|unique:finance_wallet,description',
	// ];
	// protected $fieldsStore = [
	// 	'description'
	// ];
	// protected $validateUpdate = [
	// 	'description'   => 'required|string',
	// 	'json'          => 'nullable|string',
	// 	'enable'        => 'required|integer',
	// 	'panel'         => 'required|integer',
	// ];
	// protected $fieldsUpdate = [
	// 	'description',
	// 	'json',
	// 	'enable',
	// 	'panel',
	// ];
	// private $itemTagService;
	// private $itemObsService;

	// public function __construct()
	// {
	// }
}

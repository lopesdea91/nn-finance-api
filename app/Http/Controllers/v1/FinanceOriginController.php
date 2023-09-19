<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Origin\FinanceOriginResource;
use App\Models\FinanceOriginModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FinanceOriginController
{
	private $nameSingle = 'origin';

	public function all(Request $request)
	{
		try {
			$query = $request->all();

			$params = [
				'where' => [],
				'page' => [],
			];
			# WHERE 
			if (key_exists('_q',          $query))  $params['where'][] = ['description', 'like', "%{$query['_q']}%"];
			if (key_exists('type_id',     $query))  $params['where'][] = ['type_id',     '=',    $query['type_id']];
			if (key_exists('parent_id', 	$query))  $params['where'][] = ['parent_id', 	 '=',    $query['parent_id']];
			if (key_exists('wallet_id', 	$query))  $params['where'][] = ['wallet_id', 	 '=',    $query['wallet_id']];
			if (key_exists('deleted',     $query))  $params['where']['deleted'] = $query['deleted'];

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
			'type_id',
			'parent_id',
			'wallet_id',
			'deleted_at',
		];

		$model = $onlyTrashed
			? FinanceOriginModel::onlyTrashed()->select($columns)
			: FinanceOriginModel::withTrashed(false)->select($columns);

		$result = $model->with([
			'type',
			'parent',
			'wallet' => function ($q) {
				$q->where('user_id', Auth::user()->id);
			}
		])
			->where($where)
			->orderByRaw($order)
			->paginate($limit);

		return [
			'count' => count($result->items()),
			'data' => [
				"items"     => FinanceOriginResource::collection($result->items()),
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
			'type_id',
			'parent_id',
			'wallet_id',
			'deleted_at',
		];

		$model = $onlyTrashed
			? FinanceOriginModel::onlyTrashed()->select($columns)
			: FinanceOriginModel::withTrashed(false)->select($columns);

		$result = $model
			->with([
				'type',
				'parent',
				'wallet' => function ($q) {
					$q->where('user_id', Auth::user()->id);
				}
			])
			->where($where);

		return [
			'count' => $result->count(),
			'data' => FinanceOriginResource::collection($result->get()),
		];
	}
	public function id($id)
	{
		try {
			$result = FinanceOriginModel::withTrashed()->select(
				'id',
				'description',
				'type_id',
				'parent_id',
				'wallet_id',
				"deleted_at"
			)
				->with(
					'type',
					'parent',
					'wallet'
				)
				->find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$rtn = new FinanceOriginResource($result);
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
				'type_id'       => 'nullable|integer',
				'parent_id'       => 'nullable|integer',
				'wallet_id'     => 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'parent_id',
				'wallet_id',
			]);

			FinanceOriginModel::create([
				'description' => $fields['description'],
				'type_id' 		=> $fields['type_id'],
				'parent_id' 	=> $fields['parent_id'],
				'wallet_id' 	=> $fields['wallet_id'],
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
		$result = FinanceOriginModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$request->validate([
				'description'   => 'required|string',
				'type_id'       => 'nullable|integer',
				'parent_id'     => 'nullable|integer',
				'wallet_id'     => 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'parent_id',
				'wallet_id',
			]);

			$result->update([
				'description' => $fields['description'],
				'type_id' 		=> $fields['type_id'],
				'parent_id' 	=> $fields['parent_id'],
				'wallet_id' 	=> $fields['wallet_id'],
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
		$result = FinanceOriginModel::withTrashed()->find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			if (!!$result->deleted_at) {
				$result->forceDelete();
			} else {
				$result->delete();
			}

			$rtn = ['message' => "{$this->nameSingle} deletado"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function restore($id)
	{
		$result = FinanceOriginModel::onlyTrashed()->find($id);

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
}

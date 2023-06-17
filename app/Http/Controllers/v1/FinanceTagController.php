<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Tag\FinanceTagResource;
use App\Models\FinanceTagModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FinanceTagController
{
	private $nameSingle = 'tag';
	private $nameMultiple = 'tags';

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
			if (key_exists('enable',      $query))  $params['where'][] = ['enable',      '=',    $query['enable']];
			if (key_exists('type_id',     $query))  $params['where'][] = ['type_id',     '=',    $query['type_id']];
			if (key_exists('wallet_id',   $query))  $params['where'][] = ['wallet_id',   '=',    $query['wallet_id']];
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

		$result = FinanceTagModel::select(
			"id",
			"description",
			"enable",
			"type_id",
			"wallet_id"
		)
			->with([
				'type',
				'wallet' => function ($q) {
					$q->where('user_id', Auth::user()->id);
				}
			])
			->where($where)
			->orderByRaw($order)
			->paginate($limit);

		return [
			'count' => $result->count(),
			'data' => [
				"items"     => FinanceTagResource::collection($result->items()),
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

		$result = FinanceTagModel::select(
			"id",
			"description",
			"enable",
			"type_id",
			"wallet_id"
		)
			->with([
				'type',
				'wallet' => function ($q) {
					$q->where('user_id', Auth::user()->id);
				}
			])
			->where($where);

		return [
			'count' => $result->count(),
			'data' => FinanceTagResource::collection($result->get()),
		];
	}
	public function id($id)
	{
		try {
			$result = FinanceTagModel::select(
				"id",
				"description",
				"enable",
				"type_id",
				"wallet_id",
				"created_at",
				"updated_at"
			)
				->with(
					'type',
					'wallet'
				)
				->find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$rtn = new FinanceTagResource($result);
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
				'wallet_id'     => 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'wallet_id',
			]);

			FinanceTagModel::create([
				'description' => $fields['description'],
				'enable' 			=> 1,
				'type_id' 		=> $fields['type_id'],
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
		try {
			$result = FinanceTagModel::find($id);

			if (!$result)
				throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

			$request->validate([
				'description'   => 'required|string',
				'enable'       	=> 'nullable|integer',
				'type_id'       => 'nullable|integer',
				'wallet_id'     => 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'enable',
				'type_id',
				'wallet_id',
			]);

			$result->update([
				'description' => $fields['description'],
				'enable' 			=> $fields['enable'],
				'type_id' 		=> $fields['type_id'],
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
		$result = FinanceTagModel::find($id);

		if (!$result)
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$result->delete();

			$rtn = ['message' => "{$this->nameSingle} deletado"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function enabled($id)
	{
		$result = FinanceTagModel::find($id);

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
		$result = FinanceTagModel::find($id);

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
}

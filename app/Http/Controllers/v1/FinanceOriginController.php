<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Origin\FinanceOriginListResource;
use App\Http\Resources\Finance\Origin\FinanceOriginPageResource;
use App\Http\Resources\Finance\Origin\FinanceOriginResource;
use App\Repositories\FinanceOriginRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceOriginController
{
	private $nameSingle = 'origem';

	public function get(Request $request, FinanceOriginRepository $financeOriginRepository)
	{
		try {
			$isPaginage = $request->get('_paginate');

			$search = $request->only([
				'_q',
				'_sort',
				'_order',
				'_limit',
				'_trashed',
				// filter 
				'type_id',
				'parent_id',
				'wallet_id',
				'user_id',
			]);

			$data = $isPaginage
				? new FinanceOriginPageResource($financeOriginRepository->getPage($search))
				: FinanceOriginListResource::collection($financeOriginRepository->get($search));

			$hasContent = $data->count();

			if ($hasContent) {
				$rtn = $data;
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
	public function getById($id, Request $request, FinanceOriginRepository $financeOriginRepository)
	{
		if (!$financeOriginRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$search = $request->only([
				'_q',
				// filter 
				'type_id',
				'parent_id',
				'wallet_id',
				'user_id',
			]);

			$data = $financeOriginRepository->getById($id, $search);

			if (!!$data) {
				$rtn = new FinanceOriginResource($data);
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
	public function create(Request $request, FinanceOriginRepository $financeOriginRepository)
	{
		try {
			$request->validate([
				'description'   => 'required|string',
				'type_id'       => 'required|integer',
				'parent_id'     => 'nullable|integer',
				'wallet_id'     => 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'parent_id',
				'wallet_id',
			]);

			$resultCreate = $financeOriginRepository->create($fields);

			if (!!$resultCreate) {
				$rtn = ['message' => "{$this->nameSingle} cadastrada"];
				$sts = Response::HTTP_CREATED;
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
	public function update($id, Request $request, FinanceOriginRepository $financeOriginRepository)
	{
		if (!$financeOriginRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$request->validate([
				'description'   => 'required|string',
				'type_id'       => 'required|integer',
				'parent_id'     => 'nullable|integer',
				'wallet_id'     => 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'parent_id',
				'wallet_id',
			]);

			$resultUpdate = $financeOriginRepository->update($id, $fields);

			if (!!$resultUpdate) {
				$rtn = ['message' => "{$this->nameSingle} atualizada"];
				$sts = Response::HTTP_CREATED;
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
	public function delete($id, FinanceOriginRepository $financeOriginRepository)
	{
		if (!$financeOriginRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$find = $financeOriginRepository->findDelete($id);

			if (!!$find->deleted_at) {
				$find->forceDelete();
			} else {
				$find->delete();
			}

			$rtn = ['message' => "{$this->nameSingle} deletado"];
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}

	public function restore($id, FinanceOriginRepository $financeOriginRepository)
	{
		if (!$financeOriginRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não esta deletado para restaurar!");

		try {
			$financeOriginRepository->findRestore($id)->restore();

			$rtn = ['message' => "{$this->nameSingle} restaurada!"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

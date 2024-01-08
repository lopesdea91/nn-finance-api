<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Tag\FinanceTagListResource;
use App\Http\Resources\Finance\Tag\FinanceTagPageResource;
use App\Http\Resources\Finance\Tag\FinanceTagResource;
use App\Repositories\FinanceTagRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceTagController
{
	private $nameSingle = 'tag';

	public function get(Request $request, FinanceTagRepository $financeTagRepository)
	{
		try {
			$isPaginage = $request->get('_paginate');

			$search = $request->only([
				'_q',
				'_sort',
				'_order',
				'_limit',
				'_page',
				'_trashed',
				// filter 
				'type_id',
				'wallet_id',
				'user_id',
			]);

			$data = $isPaginage
				? new FinanceTagPageResource($financeTagRepository->getPage($search))
				: FinanceTagListResource::collection($financeTagRepository->get($search));

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
	public function getById($id, Request $request, FinanceTagRepository $financeTagRepository)
	{
		if (!$financeTagRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$search = $request->only([
				'_q',
				// filter 
				'type_id',
				'wallet_id',
				'user_id',
			]);

			$data = $financeTagRepository->getById($id, $search);

			if (!!$data) {
				$rtn = new FinanceTagResource($data);
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
	public function create(Request $request, FinanceTagRepository $financeTagRepository)
	{
		try {
			$request->validate([
				'description'	=> 'required|string',
				'type_id'    	=> 'nullable|integer',
				'wallet_id'  	=> 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'wallet_id',
			]);

			$resultCreate = $financeTagRepository->create($fields);

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
	public function update($id, Request $request, FinanceTagRepository $financeTagRepository)
	{
		if (!$financeTagRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$request->validate([
				'description'	=> 'required|string',
				'type_id'    	=> 'nullable|integer',
				'wallet_id'  	=> 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'type_id',
				'wallet_id',
			]);

			$resultUpdate = $financeTagRepository->update($id, $fields);

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
	public function delete($id, FinanceTagRepository $financeTagRepository)
	{
		if (!$financeTagRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$find = $financeTagRepository->findDelete($id);

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
	public function restore($id, FinanceTagRepository $financeTagRepository)
	{
		if (!$financeTagRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não esta deletado para restaurar!");

		try {
			$financeTagRepository->findRestore($id)->restore();

			$rtn = ['message' => "{$this->nameSingle} restaurada!"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

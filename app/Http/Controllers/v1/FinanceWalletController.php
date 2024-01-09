<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\Wallet\FinanceWalletListResource;
use App\Http\Resources\Finance\Wallet\FinanceWalletPageResource;
use App\Http\Resources\Finance\Wallet\FinanceWalletResource;
use App\Repositories\FinanceWalletRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FinanceWalletController
{
	private $nameSingle = 'wallet';

	public function get(Request $request, FinanceWalletRepository $financeWalletRepository)
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
				'panel',
				'user_id',
			]);

			// dd($isPaginage);

			$data = $isPaginage
				? new FinanceWalletPageResource($financeWalletRepository->getPage($search))
				: FinanceWalletListResource::collection($financeWalletRepository->get($search));

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
	public function getById($id, Request $request, FinanceWalletRepository $financeWalletRepository)
	{
		if (!$financeWalletRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$search = $request->only([
				'_q',
				// filter 
				'panel',
				'user_id',
			]);

			$data = $financeWalletRepository->getById($id, $search);

			if (!!$data) {
				$rtn = new FinanceWalletResource($data);
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
	public function create(Request $request, FinanceWalletRepository $financeWalletRepository)
	{
		try {
			$request->validate([
				'description'   => 'required|string',
			]);

			$fields = $request->only([
				'description',
			]);

			$resultCreate = $financeWalletRepository->create($fields);

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
	public function update($id, Request $request, FinanceWalletRepository $financeWalletRepository)
	{
		if (!$financeWalletRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$request->validate([
				'description' 	=> 'required|string',
				'panel' 				=> 'required|integer',
			]);

			$fields = $request->only([
				'description',
				'panel',
			]);

			// set panel=0 all wallet
			if ((bool) $fields['panel']) {
				$financeWalletRepository->get()->map(function ($item) use ($id) {
					if ($item->id != $id) {
						$item->fill(['panel' => "0"]);
						$item->save();
					}
				});
			}

			$resultUpdate = $financeWalletRepository->update($id, $fields);

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
	public function delete($id, FinanceWalletRepository $financeWalletRepository)
	{
		if (!$financeWalletRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$find = $financeWalletRepository->findDelete($id);

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
	public function restore($id, FinanceWalletRepository $financeWalletRepository)
	{
		if (!$financeWalletRepository->has($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não esta deletado para restaurar!");

		try {
			$financeWalletRepository->findRestore($id)->restore();

			$rtn = ['message' => "{$this->nameSingle} restaurada!"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

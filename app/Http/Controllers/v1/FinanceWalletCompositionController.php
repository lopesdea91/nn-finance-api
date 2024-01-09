<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Resources\Finance\WalletComposition\FinanceWalletConsolidationListResource;
use App\Repositories\FinanceWalletCompositionRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceWalletCompositionController
{
	public function get(Request $request, FinanceWalletCompositionRepository $financeWalletCompositionRepository)
	{
		try {
			$request->validate([
				'wallet_id' => 'required|exists:finance_wallet,id',
			]);

			$search = $request->only([
				'wallet_id',
			]);

			$data = FinanceWalletConsolidationListResource::collection(
				$financeWalletCompositionRepository->get($search)
			);

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
	public function create(Request $request, FinanceWalletCompositionRepository $financeWalletCompositionRepository)
	{
		try {
			$request->validate([
				'wallet_id' 	=> 'required|exists:finance_wallet,id',
				'composition' => 'required',
			]);

			$search = $request->only([
				'wallet_id',
			]);

			$fields = $request->only([
				'wallet_id',
				'composition'
			]);

			// delete all old walletComposition
			$financeWalletCompositionRepository->get($search)->map(function ($item) {
				$item->delete();
			});

			// create new
			foreach ($fields['composition'] as $value) {
				$financeWalletCompositionRepository->create([
					'percentage_limit' 	=> $value['percentage_limit'],
					'tag_id' 						=> $value['tag_id'],
					'wallet_id' 				=> $fields['wallet_id'],
				]);
			}

			$rtn = ['message' => "Composição cadastrada!"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
	public function delete($id, Request $request, FinanceWalletCompositionRepository $financeWalletCompositionRepository)
	{
		$request->validate([
			'wallet_id' => 'required|exists:finance_wallet,id',
		]);

		$search = $request->only([
			'wallet_id',
		]);

		$financeWalletComposition = $financeWalletCompositionRepository->query($search)->find($id);

		if (!$financeWalletComposition)
			throw new ApiExceptionResponse("consolidation: id ($id) não existe!");

		try {
			$rtn = $financeWalletComposition->delete();
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

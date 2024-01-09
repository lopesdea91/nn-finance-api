<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Finance\OriginType\FinanceOriginTypeListResource;
use App\Repositories\FinanceOriginTypeRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceOriginTypeController
{
	public function get(Request $request, FinanceOriginTypeRepository $financeOriginTypeRepository)
	{
		try {
			$search = $request->only([
				'_q',
			]);

			$data = FinanceOriginTypeListResource::collection($financeOriginTypeRepository->get($search));

			$hasContent = $data->count();

			if ($hasContent) {
				$rtn = $data;
				$sts = Response::HTTP_OK;
			} else {
				$rtn = null;
				$sts = Response::HTTP_NO_CONTENT;
			}
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Finance\Status\FinanceStatusListResource;
use App\Repositories\FinanceStatusRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceStatusController
{
	public function get(Request $request, FinanceStatusRepository $financeStatusRepository)
	{
		try {
			$search = $request->only([
				'_q',
			]);

			$data = FinanceStatusListResource::collection($financeStatusRepository->get($search));

			$hasContent = $data->count();

			if ($hasContent) {
				$rtn = $data;
				$sts = Response::HTTP_OK;
			} else {
				$rtn = null;
				$sts = Response::HTTP_NO_CONTENT;
			}
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

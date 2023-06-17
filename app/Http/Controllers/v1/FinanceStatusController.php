<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Finance\Status\FinanceStatusResource;
use App\Models\FinanceStatusModel;
use Symfony\Component\HttpFoundation\Response;

class FinanceStatusController
{
	public function all()
	{
		try {
			$all = FinanceStatusModel::select('id', 'description')->get();

			$rtn = FinanceStatusResource::collection($all);
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Finance\OriginType\FinanceOriginTypeResource;
use App\Models\FinanceOriginTypeModel;
use Symfony\Component\HttpFoundation\Response;

class FinanceOriginTypeController
{
	public function all()
	{
		try {
			$all = FinanceOriginTypeModel::select('id', 'description')->get();

			$rtn = FinanceOriginTypeResource::collection($all);
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

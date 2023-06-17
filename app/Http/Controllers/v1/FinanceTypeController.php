<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Finance\Type\FinanceTypeResource;
use App\Models\FinanceTypeModel;
use Symfony\Component\HttpFoundation\Response;

class FinanceTypeController
{
	public function all()
	{
		try {
			$all = FinanceTypeModel::select('id', 'description')->get();

			$rtn = FinanceTypeResource::collection($all);
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

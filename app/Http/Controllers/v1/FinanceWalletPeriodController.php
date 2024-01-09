<?php

namespace App\Http\Controllers\v1;

use App\Models\FinanceItemModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FinanceWalletPeriodController
{
	public function get(Request $request)
	{
		try {
			$request->validate([
				'wallet_id' => 'required|integer',
				// 'period' 		=> 'string',
				// 'format' 		=> 'string',
			]);

			$fields = $request->only([
				'wallet_id',
				// 'period',
				// 'format'
			]);

			$items = FinanceItemModel::where([
				'wallet_id' => $fields['wallet_id'],
			])
				->selectRaw(DB::raw("DATE_FORMAT(date, '%Y-%m') as period"))
				->groupBy(DB::raw('period'))
				->get()
				->map(function ($value) {
					$ex_period = explode('-', $value->period);
					$current_now = now()->setDate($ex_period[0], $ex_period[1], 01);

					$value->year = $current_now->format('Y');
					$value->month = $current_now->format('m');
					$value->label = $current_now->format('m-Y');

					return $value;
				})
				->toArray();

			$rtn = $items;
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {
			$rtn = ['message' => $e->getMessage()];
			$sts = Response::HTTP_FAILED_DEPENDENCY;
		}

		return response()->json($rtn, $sts);
	}
}

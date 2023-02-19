<?php

namespace App\Http\Controllers\v1;

use App\Services\FinanceService;

class FinanceController
{
	public function data()
	{
		return FinanceService::data();
	}
}

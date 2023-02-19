<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\FinanceStatusService;

class FinanceStatusController extends CrudController
{
	protected $nameSingle = 'status';
	protected $nameMultiple = 'status';
	protected $service;
	protected $resource = 'App\Http\Resources\Finance\Status\FinanceStatusResource';
	protected $collection = 'App\Http\Resources\Finance\Status\FinanceStatusCollection';

	function __construct(FinanceStatusService $service)
	{
		$this->service = $service;
	}
}

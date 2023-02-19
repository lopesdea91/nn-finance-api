<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\FinanceOriginTypeService;

class FinanceOriginTypeController extends CrudController
{
	protected $nameSingle = 'origin-type';
	protected $nameMultiple = 'origin-types';
	protected $service;
	protected $resource = 'App\Http\Resources\Finance\OriginType\FinanceOriginTypeResource';
	protected $collection = 'App\Http\Resources\Finance\OriginType\FinanceOriginTypeCollection';

	function __construct(FinanceOriginTypeService $service)
	{
		$this->service = $service;
	}
}

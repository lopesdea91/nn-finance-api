<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\FinanceTypeService;

class FinanceTypeController extends CrudController
{
	protected $nameSingle = 'type';
	protected $nameMultiple = 'types';
	protected $service;
	protected $resource = 'App\Http\Resources\Finance\Type\FinanceTypeResource';
	protected $collection = 'App\Http\Resources\Finance\Type\FinanceTypeCollection';

	function __construct(FinanceTypeService $service)
	{
		$this->service = $service;
	}
}

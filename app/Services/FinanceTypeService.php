<?php

namespace App\Services;

use App\Repository\FinanceTypeRepository;
use App\Services\Base\BaseService;

class FinanceTypeService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new FinanceTypeRepository;
	}
}

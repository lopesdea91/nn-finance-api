<?php

namespace App\Services;

use App\Repository\FinanceOriginTypeRepository;
use App\Services\Base\BaseService;

class FinanceOriginTypeService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new FinanceOriginTypeRepository;
	}
}

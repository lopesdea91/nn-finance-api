<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repository\FinanceStatusRepository;
use App\Services\Base\BaseService;

class FinanceStatusService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new FinanceStatusRepository;
	}
}

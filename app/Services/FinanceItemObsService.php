<?php

namespace App\Services;

use App\Repository\FinanceItemObsRepository;
use App\Services\Base\BaseService;

class FinanceItemObsService extends BaseService
{
  protected $repository;

  public function __construct()
  {
    $this->repository = new FinanceItemObsRepository;
  }
}

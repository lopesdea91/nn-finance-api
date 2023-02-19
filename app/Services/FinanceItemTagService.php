<?php

namespace App\Services;

use App\Repository\FinanceItemTagRepository;
use App\Services\Base\BaseService;

class FinanceItemTagService extends BaseService
{
  protected $repository;

  public function __construct()
  {
    $this->repository = new FinanceItemTagRepository;
  }
}

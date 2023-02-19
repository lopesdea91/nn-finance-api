<?php

namespace App\Repository;

use App\Models\FinanceItemTagModel;
use App\Repository\Base\CrudRepository;

class FinanceItemTagRepository extends CrudRepository
{
  protected $model;
  protected $fields = ['id', 'item_id', 'tag_id'];
  // protected $relationships = [];

  public function __construct()
  {
    $this->model = new FinanceItemTagModel;
  }
}

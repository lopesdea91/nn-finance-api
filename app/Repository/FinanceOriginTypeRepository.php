<?php

namespace App\Repository;

use App\Models\FinanceOriginTypeModel;
use App\Repository\Base\CrudRepository;

class FinanceOriginTypeRepository extends CrudRepository
{
	protected $model;
	protected $fields = ['id', 'description'];
	// protected $relationships = [];

	public function __construct()
	{
		$this->model = new FinanceOriginTypeModel;
	}
}

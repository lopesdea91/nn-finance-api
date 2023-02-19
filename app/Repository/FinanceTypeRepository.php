<?php

namespace App\Repository;

use App\Models\FinanceTypeModel;
use App\Repository\Base\CrudRepository;

class FinanceTypeRepository extends CrudRepository
{
	protected $model;
	protected $fields = ['id', 'description'];
	// protected $relationships = [];

	public function __construct()
	{
		$this->model = new FinanceTypeModel;
	}
}

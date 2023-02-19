<?php

namespace App\Repository;

use App\Models\FinanceStatusModel;
use App\Repository\Base\CrudRepository;

class FinanceStatusRepository extends CrudRepository
{
	protected $model;
	protected $fields = ['id', 'description'];
	// protected $relationships = [];

	public function __construct()
	{
		$this->model = new FinanceStatusModel;
	}
}

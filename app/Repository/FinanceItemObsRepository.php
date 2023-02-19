<?php

namespace App\Repository;

use App\Models\FinanceItemObsModel;
use App\Repository\Base\CrudRepository;

class FinanceItemObsRepository extends CrudRepository
{
	protected $model;
	protected $fields = ['id', 'obs', 'item_id'];
	// protected $relationships = [];

	public function __construct()
	{
		$this->model = new FinanceItemObsModel;
	}
}

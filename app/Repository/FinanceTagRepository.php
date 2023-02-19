<?php

namespace App\Repository;

use App\Models\FinanceTagModel;
use App\Repository\Base\CrudRepository;

class FinanceTagRepository extends CrudRepository
{
	protected $model;
	protected $fields = ["id", "description", "enable", "type_id", "wallet_id"];
	protected $relationships = ['type', 'wallet'];

	public function __construct()
	{
		$this->model = new FinanceTagModel;
	}

	public function paginate($args)
	{
		$query =    key_exists('query', $args)    ? $args['query']    : [];
		$where =    key_exists('where', $args)    ? $args['where']    : [];
		$whereHas = key_exists('whereHas', $args) ? $args['whereHas'] : [];

		# WHERE 
		if (key_exists('_q',          $query))  $where[] = ['description', 'like', "%{$query['_q']}%"];
		if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
		if (key_exists('type_id',     $query))  $where[] = ['type_id',     '=',    $query['type_id']];
		if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];

		return parent::paginate([
			'query' => $query,
			'where' => $where,
			'whereHas' => $whereHas,
		]);
	}
}

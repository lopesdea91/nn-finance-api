<?php

namespace App\Repository;

use App\Models\FinanceOriginModel;
use App\Repository\Base\CrudRepository;

class FinanceOriginRepository extends CrudRepository
{
	protected $model;
	protected $fields = ['id', 'description', 'enable', 'type_id', 'parent_id', 'wallet_id'];
	protected $relationships = ['type', 'parent', 'wallet'];

	public function __construct()
	{
		$this->model = new FinanceOriginModel;
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
		if (key_exists('parent_id',   $query))  $where[] = ['parent_id',   '=',    $query['parent_id']];
		if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];

		return parent::paginate([
			'query' => $query,
			'where' => $where,
			'whereHas' => $whereHas,
		]);
	}
}

<?php

namespace App\Repository;

use App\Models\FinanceWalletModel;
use App\Repository\Base\CrudRepository;

class FinanceWalletRepository extends CrudRepository
{
	protected $model;
	protected $fields = ['id', 'description', 'json', 'composition', 'enable', 'panel', 'user_id'];
	protected $relationships = ['user'];

	public function __construct()
	{
		$this->model = new FinanceWalletModel;
	}

	public function paginate($args)
	{

		$query =    key_exists('query', $args)    ? $args['query']    : [];
		$where =    key_exists('where', $args)    ? $args['where']    : [];
		$whereHas = key_exists('whereHas', $args) ? $args['whereHas'] : [];

		# WHERE 
		if (key_exists('_q',          $query))  $where[] = ['description', 'like', "%{$query['_q']}%"];
		if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
		if (key_exists('panel',       $query))  $where[] = ['panel',       '=',    $query['panel']];
		if (key_exists('user_id',     $query))  $where[] = ['user_id',     '=',    $query['user_id']];

		return parent::paginate([
			'query' => $query,
			'where' => $where,
			'whereHas' => $whereHas,
		]);
	}
}

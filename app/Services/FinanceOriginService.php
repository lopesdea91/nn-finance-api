<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repository\FinanceOriginRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Auth;

class FinanceOriginService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new FinanceOriginRepository;
	}

	public function paginate($args)
	{
		$user_id = Auth::user()->id;

		return $this->repository->paginate([
			'query' => $args['query'],
			'where' => [],
			'whereHas' => [
				'wallet' => function ($q) use ($user_id) {
					$q->where('user_id', $user_id);
				}
			],
		]);
	}
	public function all($args)
	{
		$user_id = Auth::user()->id;

		return parent::all([
			'query' => $args['query'],
			'where' => [],
			'whereHas' => [
				'wallet' => function ($q) use ($user_id) {
					$q->where('user_id', $user_id);
				}
			],
		]);
	}
	public function id($id)
	{
		return $this->repository->id($id);
	}
	public function create($fields)
	{
		$createField = [
			'description' => $fields['description'],
			'enable'      => "1",
			'type_id'     => $fields['type_id'],
			'parent_id'   => $fields['parent_id'],
			'wallet_id'   => $fields['wallet_id'],
		];

		return $this->repository->create($createField);
	}
	public function update($id, $fields)
	{
		$where = [
			'id'        => $id,
			'wallet_id' => $fields['wallet_id'],
		];

		$updateField = [
			'description' => $fields['description'],
			'enable'      => $fields['enable'],
			'type_id'     => $fields['type_id'],
			'parent_id'   => $fields['parent_id'],
		];

		return $this->repository->update($where, $updateField);
	}
	public function delete($id)
	{
		$where = [
			'id' => $id,
		];

		return $this->repository->delete($where);
	}
}

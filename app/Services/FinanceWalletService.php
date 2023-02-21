<?php

namespace App\Services;

use App\Services\Base\BaseService;
use App\Repository\FinanceWalletRepository;
use Illuminate\Support\Facades\Auth;

class FinanceWalletService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new FinanceWalletRepository;
	}

	public function paginate($args)
	{
		$user_id = Auth::user()->id;

		return $this->repository->paginate([
			'query' => $args['query'],
			'where' => [
				['user_id', '=', $user_id]
			],
			'whereHas' => [],
		]);
	}
	public function all($args)
	{
		$user_id = Auth::user()->id;

		return parent::all([
			'query' => $args['query'],
			'where' => [
				'user_id' => $user_id
			],
			'whereHas' => [],
		]);
	}
	public function create($fields)
	{
		$createField = [
			'description' => $fields['description'],
			'json'        => '{}',
			'enable'      => '1',
			'panel'       => '0',
			'user_id'     => Auth::user()->id
		];

		return $this->repository->create($createField);
	}
	public function update($id, $fields)
	{
		$where = [
			'id'      => $id,
			'user_id' => Auth::user()->id
		];

		$updateField = [
			'description' => $fields['description'],
			'json'        => $fields['json'],
			'enable'      => $fields['enable'],
			'panel'       => $fields['panel'],
		];

		return $this->repository->update($where, $updateField);
	}
}

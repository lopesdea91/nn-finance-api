<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use App\Services\Base\BaseService;
// use App\Services\FinanceService;

class UserService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new UserRepository;
	}

	public function data()
	{
		$user = Auth::user();
		// $user_id = $user->id;

		$data = [];

		$data['period']     = now()->format('Y-m');
		$data['user']       = new UserResource($user);
		// $data['finance']    = (new FinanceService)::data($user_id);

		return $data;
	}

	public function create($fields)
	{
		$createField = [
			'name'        => $fields['name'],
			'email'       => $fields['email'],
			'password'    => Hash::make($fields['password']),
			'type'        => 'client',
		];

		return $this->repository->create($createField);
	}

	public function update($id, $fields)
	{
		$where = [
			'id' => $id,
		];

		$updateField = [];

		foreach (['name', 'email'] as $key) {
			if (key_exists($key, $fields)) {
				$updateField[$key] = $fields[$key];
			}
		}

		if (key_exists('password', $fields)) {
			$updateField['password'] = Hash::make($fields['password']);
		}

		return $this->repository->update($where, $updateField);
	}

	public function existByEmail($email)
	{
		return !!$this->repository->all(["email" => $email])->count();
	}
}

<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends CrudController
{
	protected $nameSingle = 'usuário';
	protected $nameMultiple = 'usuários';
	protected $service;
	protected $resource = 'App\Http\Resources\UserResource';
	protected $validateUpdate = [
		'name'			=> 'required|string',
		'email'     => 'nullable|string',
		'password'  => 'nullable|integer',
	];
	protected $fieldsUpdate = [
		'name',
		'email',
		'password',
	];

	public function __construct()
	{
		$this->service = new UserService;
	}

	public function data()
	{
		try {
			$rtn = $this->service->data();
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

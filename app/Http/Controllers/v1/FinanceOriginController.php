<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\FinanceOriginService;

class FinanceOriginController extends CrudController
{
	protected $nameSingle = 'origin';
	protected $nameMultiple = 'origins';
	protected $service;
	protected $resource = 'App\Http\Resources\Finance\Origin\FinanceOriginResource';
	protected $collection = 'App\Http\Resources\Finance\Origin\FinanceOriginCollection';
	protected $validateStore = [
		'description'   => 'required|string',
		// 'enable'        => 'required|integer',
		'type_id'       => 'nullable|integer',
		'parent_id'     => 'nullable|integer',
		'wallet_id'     => 'required|integer',
	];
	protected $fieldsStore = [
		'description',
		// 'enable',
		'type_id',
		'parent_id',
		'wallet_id',
	];
	protected $validateUpdate = [
		'description'   => 'required|string',
		'enable'        => 'required|integer',
		'type_id'       => 'nullable|integer',
		'parent_id'     => 'nullable|integer',
		'wallet_id'     => 'required|integer',
	];
	protected $fieldsUpdate = [
		'description',
		'enable',
		'type_id',
		'parent_id',
		'wallet_id',
	];

	public function __construct()
	{
		$this->service = new FinanceOriginService;
	}
}

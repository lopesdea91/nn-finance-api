<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Services\FinanceTagService;

class FinanceTagController extends CrudController
{
	protected $nameSingle = 'tag';
	protected $nameMultiple = 'tags';
	protected $service;
	protected $resource = 'App\Http\Resources\Finance\Tag\FinanceTagResource';
	protected $collection = 'App\Http\Resources\Finance\Tag\FinanceTagCollection';
	protected $validateStore = [
		'description'   => 'required|string',
		// 'enable'        => 'required|integer',
		'type_id'       => 'nullable|integer',
		'wallet_id'     => 'required|integer',
	];
	protected $fieldsStore = [
		'description',
		// 'enable',
		'type_id',
		'wallet_id',
	];
	protected $validateUpdate = [
		'description'   => 'required|string',
		'enable'        => 'required|integer',
		'type_id'       => 'nullable|integer',
		'wallet_id'     => 'required|integer',
	];
	protected $fieldsUpdate = [
		'description',
		'enable',
		'type_id',
		'wallet_id',
	];

	public function __construct()
	{
		$this->service = new FinanceTagService;
	}
}

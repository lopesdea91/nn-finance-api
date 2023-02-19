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

	// public function all(Request $request)
	// {
	//     try {
	//         $query = $request->query();

	//         $hasPaginate = (key_exists('_paginate', $query));

	//         if ($hasPaginate) {
	//             $paginate = $this->financeOriginService->paginate($request);

	//             $sts = Response::HTTP_OK;
	//             $rtn = [
	//                 "items"     => new Collection($paginate->items()),
	//                 "page"      => $paginate->currentPage(),
	//                 "total"     => $paginate->total(),
	//                 "limit"     => $paginate->perPage(),
	//                 "lastPage"  => $paginate->lastPage(),
	//             ];
	//         } else {
	//             $rtn = new Collection($this->financeOriginService->all($request));
	//             $sts = Response::HTTP_OK;
	//         }
	//     } catch (\Throwable $e) {
	//         $sts = Response::HTTP_FAILED_DEPENDENCY;
	//         $rtn = ['message' => $e->getMessage()];
	//     }

	//     return response()->json($rtn, $sts);
	// }

	// public function id($origin_id)
	// {
	//     if (!$this->financeOriginService->exist($origin_id))
	//         throw new ApiExceptionResponse("origin: id ($origin_id) não existe!");

	//     try {
	//         $rtn = new Resource($this->financeOriginService->id($origin_id));
	//         $sts = Response::HTTP_OK;
	//     } catch (\Throwable $e) {

	//         $sts = Response::HTTP_FAILED_DEPENDENCY;
	//         $rtn = ['message' => $e->getMessage()];
	//     }

	//     return response()->json($rtn, $sts);
	// }

	// public function store(StoreRequest $request)
	// {
	//     try {
	//         $fields = $request->all();

	//         $rtn = new Resource($this->financeOriginService->store($fields));

	//         $sts = Response::HTTP_CREATED;
	//     } catch (\Throwable $e) {

	//         $sts = Response::HTTP_FAILED_DEPENDENCY;
	//         $rtn = ['message' => $e->getMessage()];
	//     }

	//     return response()->json($rtn, $sts);
	// }

	// public function update(UpdateRequest $request, $origin_id)
	// {
	//     if (!$this->financeOriginService->exist($origin_id))
	//         throw new ApiExceptionResponse("origin: id ($origin_id) não existe!");

	//     try {
	//         $fields = $request->all();

	//         $rtn = new Resource($this->financeOriginService->update($origin_id, $fields));

	//         $sts = Response::HTTP_CREATED;
	//     } catch (\Throwable $e) {

	//         $sts = Response::HTTP_FAILED_DEPENDENCY;
	//         $rtn = ['message' => $e->getMessage()];
	//     }

	//     return response()->json($rtn, $sts);
	// }

	// public function delete($origin_id)
	// {
	//     if (!$this->financeOriginService->exist($origin_id))
	//         throw new ApiExceptionResponse("origin: id ($origin_id) não existe!");

	//     try {
	//         $rtn = $this->financeOriginService->delete($origin_id);
	//         $sts = Response::HTTP_NO_CONTENT;
	//     } catch (\Throwable $e) {

	//         $sts = Response::HTTP_FAILED_DEPENDENCY;
	//         $rtn = ['message' => $e->getMessage()];
	//     }

	//     return response()->json($rtn, $sts);
	// }
}

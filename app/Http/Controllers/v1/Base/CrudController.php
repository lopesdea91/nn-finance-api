<?php

namespace App\Http\Controllers\v1\Base;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrudController extends Controller
{
	protected $nameSingle;
	protected $nameMultiple;
	protected $service;
	protected $resource;
	protected $collection;
	protected $validateStore;
	protected $fieldsStore;
	protected $validateUpdate;
	protected $fieldsUpdate;

	public function all(Request $request)
	{
		try {
			$args = [
				'query' => $request->query(),
				'where' => [],
				'whereHas' => [],
			];

			$hasPaginate = key_exists('_paginate', $request->all());

			if ($hasPaginate) {

				$paginate = $this->service->paginate($args);

				$count = $paginate->count();

				if ($count) {
					$sts = Response::HTTP_OK;
					$rtn = [
						"items"     => new $this->collection($paginate->items()),
						"page"      => $paginate->currentPage(),
						"total"     => $paginate->total(),
						"limit"     => $paginate->perPage(),
						"lastPage"  => $paginate->lastPage(),
					];
				} else {
					$rtn = null;
					$sts = Response::HTTP_NO_CONTENT;
				}
			} else {
				$rtn = new $this->collection($this->service->all($args));
				$sts = Response::HTTP_OK;
			}
		} catch (\Throwable $e) {
			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function id($id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$rtn = new $this->resource($this->service->id($id));
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function store(Request $request)
	{
		$request->validate($this->validateStore);

		try {
			$fields = $request->only($this->fieldsStore);

			$rtn = new $this->resource($this->service->create($fields));
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function update(Request $request, $id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		$request->validate($this->validateUpdate);

		$fields = $request->only($this->fieldsUpdate);

		try {
			$rtn = new $this->resource($this->service->update($id, $fields));

			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function delete($id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$rtn = $this->service->delete($id);
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function enabled($id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$this->service->enabled($id);

			$rtn = ['message' => "{$this->nameSingle} ativado(a)"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function disabled($id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$this->service->disabled($id);

			$rtn = ['message' => "{$this->nameSingle} desativado(a)"];
			$sts = Response::HTTP_OK;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

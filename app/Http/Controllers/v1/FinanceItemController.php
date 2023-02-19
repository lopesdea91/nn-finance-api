<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\v1\Base\CrudController;
use App\Exceptions\ApiExceptionResponse;
use App\Services\FinanceItemService;
use App\Services\FinanceItemObsService;
use App\Services\FinanceItemTagService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class FinanceItemController extends CrudController
{
	protected $nameSingle = 'item';
	protected $nameMultiple = 'items';
	protected $service;
	protected $resource = 'App\Http\Resources\Finance\Item\FinanceItemResource';
	protected $collection = 'App\Http\Resources\Finance\Item\FinanceItemCollection';
	protected $validateStore = [
		'description' => 'required|unique:finance_wallet,description',
	];
	protected $fieldsStore = [
		'description'
	];
	protected $validateUpdate = [
		'description'   => 'required|string',
		'json'          => 'nullable|string',
		'enable'        => 'required|integer',
		'panel'         => 'required|integer',
	];
	protected $fieldsUpdate = [
		'description',
		'json',
		'enable',
		'panel',
	];
	private $itemTagService;
	private $itemObsService;

	public function __construct()
	{
		$this->itemTagService = new FinanceItemTagService;
		$this->itemObsService = new FinanceItemObsService;
		$this->service = new FinanceItemService;
	}

	public function storeItem(Request $request)
	{
		$request->validate([
			"value"       => 'required|numeric',
			"date"        => 'required|string',
			"obs"         => 'required|string',
			"sort"        => 'required|integer',
			"enable"      => 'required|integer',
			"enable"      => 'required|integer',
			"repeat"      => ['required', Rule::in('UNIQUE', 'REPEAT')],
			"origin_id"   => 'required|exists:finance_origin,id',
			"status_id"   => 'required|exists:finance_status,id',
			"type_id"     => 'required|exists:finance_type,id',
			"tag_ids"    => 'required',
			// "category_id" => 'required|exists:finance_category,id',
			// "group_id"    => 'required|exists:finance_group,id',
			"wallet_id"   => 'required|exists:finance_wallet,id',
		]);

		$fields = $request->only([
			"value",
			"date",
			"obs",
			"sort",
			"enable",
			"enable",
			"repeat",
			"origin_id",
			"status_id",
			"type_id",
			"tag_ids",
			"wallet_id",
		]);

		try {
			new $this->resource($this->service->store($fields));

			$rtn = ['message' => "{$this->nameSingle} criado!"];
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function updateItem(Request $request, $id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		$request->validate([
			"value"       => 'required|numeric',
			"date"        => 'required|string',
			"obs"         => 'required|string',
			"sort"        => 'required|integer',
			"enable"      => 'required|integer',
			"enable"      => 'required|integer',
			"repeat"      => ['required', Rule::in('UNIQUE', 'REPEAT')],
			"origin_id"   => 'required|exists:finance_origin,id',
			"status_id"   => 'required|exists:finance_status,id',
			"type_id"     => 'required|exists:finance_type,id',
			"tag_ids"    => 'required',
			// "category_id" => 'required|exists:finance_category,id',
			// "group_id"    => 'required|exists:finance_group,id',
			"wallet_id"   => 'required|exists:finance_wallet,id',
		]);

		$fields = $request->only([
			"value",
			"date",
			"obs",
			"sort",
			"enable",
			"enable",
			"repeat",
			"origin_id",
			"status_id",
			"type_id",
			"tag_ids",
			"wallet_id",
		]);

		try {
			new $this->resource($this->service->update($id, $fields));

			$rtn = ['message' => "{$this->nameSingle} atualizado!"];
			$sts = Response::HTTP_CREATED;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}

	public function deleteItem($id)
	{
		if (!$this->service->exist($id))
			throw new ApiExceptionResponse("{$this->nameSingle}: id ($id) não existe!");

		try {
			$this->itemObsService->query([
				'where' => [
					'item_id' => $id
				]
			])->delete();

			$this->itemTagService->query([
				'where' => [
					'item_id' => $id
				]
			])->delete();

			$this->service->delete($id);

			$rtn = null;
			$sts = Response::HTTP_NO_CONTENT;
		} catch (\Throwable $e) {

			$sts = Response::HTTP_FAILED_DEPENDENCY;
			$rtn = ['message' => $e->getMessage()];
		}

		return response()->json($rtn, $sts);
	}
}

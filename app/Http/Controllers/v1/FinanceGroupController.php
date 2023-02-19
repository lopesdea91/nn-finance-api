<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FinanceGroupService;
use App\Http\Requests\Finance\Group\{
    FinanceGroupStoreRequest as StoreRequest,
    FinanceGroupUpdateRequest as UpdateRequest
};
use App\Http\Resources\Finance\Group\{
    FinanceGroupResource as Resource,
    FinanceGroupCollection as Collection
};

class FinanceGroupController extends Controller
{
    private $financeGroupService;
    function __construct(FinanceGroupService $financeGroupService)
    {
        $this->financeGroupService = $financeGroupService;
    }

    public function all(Request $request)
    {
        try {
            $query = $request->query();

            $hasPaginate = key_exists('_paginate', $query);

            if ($hasPaginate) {
                $paginate = $this->financeGroupService->paginate($request);

                $sts = Response::HTTP_OK;
                $rtn = [
                    "items"     => new Collection($paginate->items()),
                    "page"      => $paginate->currentPage(),
                    "total"     => $paginate->total(),
                    "limit"     => $paginate->perPage(),
                    "lastPage"  => $paginate->lastPage(),
                ];
            } else {
                $rtn = new Collection($this->financeGroupService->all($request));
                $sts = Response::HTTP_OK;
            }
        } catch (\Throwable $e) {
            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function id($group_id)
    {
        if (!$this->financeGroupService->exist($group_id))
            throw new ApiExceptionResponse("Group: id ($group_id) não existe!");

        try {
            $rtn = new Resource($this->financeGroupService->id($group_id));
            $sts = Response::HTTP_OK;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function store(StoreRequest $request)
    {
        try {
            $fields = $request->all();


            $rtn = new Resource($this->financeGroupService->store($fields));

            $sts = Response::HTTP_CREATED;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function update(UpdateRequest $request, $group_id)
    {
        if (!$this->financeGroupService->exist($group_id))
            throw new ApiExceptionResponse("Group: id ($group_id) não existe!");

        try {
            $fields = $request->all();

            $rtn = new Resource($this->financeGroupService->update($group_id, $fields));

            $sts = Response::HTTP_CREATED;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function delete($group_id)
    {
        if (!$this->financeGroupService->exist($group_id))
            throw new ApiExceptionResponse("Group: id ($group_id) não existe!");

        try {
            $rtn = $this->financeGroupService->delete($group_id);
            $sts = Response::HTTP_NO_CONTENT;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }
}

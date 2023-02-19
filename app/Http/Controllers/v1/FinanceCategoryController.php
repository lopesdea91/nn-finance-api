<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ApiExceptionResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FinanceCategoryService;
use App\Http\Requests\Finance\Category\{
    FinanceCategoryStoreRequest as StoreRequest,
    FinanceCategoryUpdateRequest as UpdateRequest
};
use App\Http\Resources\Finance\Category\{
    FinanceCategoryResource as Resource,
    FinanceCategoryCollection as Collection
};

class FinanceCategoryController extends Controller
{
    private $financeCategoryService;

    function __construct(FinanceCategoryService $financeCategoryService)
    {
        $this->financeCategoryService = $financeCategoryService;
    }

    public function all(Request $request)
    {
        try {
            $query = $request->query();
            $hasPaginate = key_exists('_paginate', $query);

            if ($hasPaginate) {
                $paginate = $this->financeCategoryService->paginate($request);

                $sts = Response::HTTP_OK;
                $rtn = [
                    "items"     => new Collection($paginate->items()),
                    "page"      => $paginate->currentPage(),
                    "total"     => $paginate->total(),
                    "limit"     => $paginate->perPage(),
                    "lastPage"  => $paginate->lastPage(),
                ];
            } else {
                $rtn = new Collection($this->financeCategoryService->all($request));
                $sts = Response::HTTP_OK;
            }
        } catch (\Throwable $e) {
            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function id($category_id)
    {
        if (!$this->financeCategoryService->exist($category_id))
            throw new ApiExceptionResponse("category: id ($category_id) não existe!");

        try {
            $rtn = new Resource($this->financeCategoryService->id($category_id));
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

            $rtn = new Resource($this->financeCategoryService->store($fields));

            $sts = Response::HTTP_CREATED;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function update(UpdateRequest $request, $category_id)
    {
        if (!$this->financeCategoryService->exist($category_id))
            throw new ApiExceptionResponse("category: id ($category_id) não existe!");

        try {
            $fields = $request->all();

            $rtn = new Resource($this->financeCategoryService->update($category_id, $fields));

            $sts = Response::HTTP_CREATED;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }

    public function delete($category_id)
    {
        if (!$this->financeCategoryService->exist($category_id))
            throw new ApiExceptionResponse("category: id ($category_id) não existe!");

        try {
            $rtn = $this->financeCategoryService->delete($category_id);
            $sts = Response::HTTP_NO_CONTENT;
        } catch (\Throwable $e) {

            $sts = Response::HTTP_FAILED_DEPENDENCY;
            $rtn = ['message' => $e->getMessage()];
        }

        return response()->json($rtn, $sts);
    }
}

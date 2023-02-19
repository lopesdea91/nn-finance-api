<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repository\FinanceCategoryRepository;

class FinanceCategoryService
{
    public function paginate(Request $request)
    {
        $items = FinanceCategoryRepository::paginate($request);

        return $items;
    }
    public function all(Request $request)
    {
        $items = FinanceCategoryRepository::all($request);

        return $items;
    }
    public function id($id)
    {
        $item = FinanceCategoryRepository::id($id);

        return $item;
    }
    public function store($fields)
    {
        $store = FinanceCategoryRepository::store(
            array_merge(
                [
                    'json' => '{}',
                    'enable' => '1',
                    'panel' => '0',
                ],
                $fields
            )
        );

        return FinanceCategoryRepository::id($store->id);
    }
    public function update($id, $fields)
    {
        FinanceCategoryRepository::update($id, $fields);

        return FinanceCategoryRepository::id($id);
    }
    public function delete($id)
    {
        return FinanceCategoryRepository::delete($id);
    }
    public function exist($id)
    {
        return !!FinanceCategoryRepository::id($id);
    }
}

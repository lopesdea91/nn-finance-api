<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repository\FinanceGroupRepository;

class FinanceGroupService
{
    public function paginate(Request $request)
    {
        $items = FinanceGroupRepository::paginate($request);

        return $items;
    }
    public function all(Request $request)
    {
        $items = FinanceGroupRepository::all($request);

        return $items;
    }
    public function id($id)
    {
        $item = FinanceGroupRepository::id($id);

        return $item;
    }
    public function store($fields)
    {
        $store = FinanceGroupRepository::store(
            array_merge(
                [
                    'enable' => '1',
                ],
                $fields
            )
        );

        return FinanceGroupRepository::id($store->id);
    }
    public function update($id, $fields)
    {
        FinanceGroupRepository::update($id, $fields);

        return FinanceGroupRepository::id($id);
    }
    public function delete($id)
    {
        return FinanceGroupRepository::delete($id);
    }

    public function exist($id)
    {
        return !!FinanceGroupRepository::id($id);
    }
}

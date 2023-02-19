<?php

namespace App\Http\Resources\Finance\Group;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceGroupCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Group\FinanceGroupResource';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource;
    }
}

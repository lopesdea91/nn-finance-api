<?php

namespace App\Http\Resources\Finance\List;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceListCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\List\FinanceListResource';

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

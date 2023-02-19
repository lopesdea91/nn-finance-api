<?php

namespace App\Http\Resources\Finance\OriginType;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceOriginTypeCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\OriginType\FinanceOriginTypeResource';

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

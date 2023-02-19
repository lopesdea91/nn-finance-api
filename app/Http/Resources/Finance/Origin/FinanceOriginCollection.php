<?php

namespace App\Http\Resources\Finance\Origin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceOriginCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Origin\FinanceOriginResource';

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

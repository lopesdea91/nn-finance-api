<?php

namespace App\Http\Resources\Finance\Item;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceItemCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Item\FinanceItemResource';

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

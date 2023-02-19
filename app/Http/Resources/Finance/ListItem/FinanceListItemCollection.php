<?php

namespace App\Http\Resources\Finance\ListItem;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceListItemCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\ListItem\FinanceListItemResource';

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

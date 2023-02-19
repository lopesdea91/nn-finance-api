<?php

namespace App\Http\Resources\Finance\Tag;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceTagCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Tag\FinanceTagResource';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource;
    }
}

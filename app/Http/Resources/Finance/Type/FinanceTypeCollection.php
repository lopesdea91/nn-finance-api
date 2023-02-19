<?php

namespace App\Http\Resources\Finance\Type;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceTypeCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Type\FinanceTypeResource';

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

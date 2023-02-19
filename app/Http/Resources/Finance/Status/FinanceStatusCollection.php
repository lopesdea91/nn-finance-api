<?php

namespace App\Http\Resources\Finance\Status;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceStatusCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Status\FinanceStatusResource';

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

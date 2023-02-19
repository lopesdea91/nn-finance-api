<?php

namespace App\Http\Resources\Finance\Invoice;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceInvoiceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\Invoice\FinanceInvoiceResource';

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

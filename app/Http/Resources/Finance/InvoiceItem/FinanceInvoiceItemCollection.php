<?php

namespace App\Http\Resources\Finance\InvoiceItem;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceInvoiceItemCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Finance\InvoiceItem\FinanceInvoiceItemResource';

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

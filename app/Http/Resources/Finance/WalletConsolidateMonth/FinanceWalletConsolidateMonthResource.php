<?php

namespace App\Http\Resources\Finance\WalletConsolidateMonth;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletConsolidateMonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}

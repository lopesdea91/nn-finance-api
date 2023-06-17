<?php

namespace App\Http\Resources\Finance\WalletComposition;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletConsolidationOriginCreditResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'sum' => $this->sum,
      'consolidation_id' => $this->consolidation_id,
      'origin' => [
        'id' => $this->origin->id,
        'description' => $this->origin->description
      ]
    ];
  }
}

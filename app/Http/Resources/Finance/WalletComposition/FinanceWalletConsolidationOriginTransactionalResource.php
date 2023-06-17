<?php

namespace App\Http\Resources\Finance\WalletComposition;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletConsolidationOriginTransactionalResource extends JsonResource
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
      'revenue' => $this->revenue,
      'expense' => $this->expense,
      'average' => $this->average,
      'consolidation_id' => $this->consolidation_id,
      'origin' => [
        'id' => $this->origin->id,
        'description' => $this->origin->description
      ]
    ];
  }
}

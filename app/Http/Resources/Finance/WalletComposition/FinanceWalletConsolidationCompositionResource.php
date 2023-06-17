<?php

namespace App\Http\Resources\Finance\WalletComposition;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletConsolidationCompositionResource extends JsonResource
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
      'value_current' => $this->value_current,
      'value_limit' => $this->value_limit,
      'percentage_limit' => $this->percentage_limit,
      'percentage_current' => $this->percentage_current,
      'consolidation_id' => $this->consolidation_id,
      'tag' => [
        'id' => $this->tag->id,
        'description' => $this->tag->description
      ]
    ];
  }
}

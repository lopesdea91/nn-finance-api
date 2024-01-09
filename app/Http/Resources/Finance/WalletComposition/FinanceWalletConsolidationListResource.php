<?php

namespace App\Http\Resources\Finance\WalletComposition;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletConsolidationListResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'percentage_limit' => $this->percentage_limit,
      'tag_id' => $this->tag_id,
      // 'tag' => [
      //   'id' => $this->tag->id,
      //   'description' => $this->tag->description,
      //   'type_id' => $this->tag->type_id,
      // ],
      'wallet_id' => $this->wallet_id,
      // 'wallet' => [
      //   'id' => $this->wallet->id,
      //   'description' => $this->wallet->description,
      // ] 
    ];
  }
}

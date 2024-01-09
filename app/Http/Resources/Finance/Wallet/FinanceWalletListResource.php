<?php

namespace App\Http\Resources\Finance\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletListResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'id'            => $this->id,
			'description'   => $this->description,
			'panel'         => intval($this->panel),
		];
	}
}

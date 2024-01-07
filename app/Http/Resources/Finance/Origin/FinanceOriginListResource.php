<?php

namespace App\Http\Resources\Finance\Origin;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOriginListResource extends JsonResource
{
	public function toArray($request)
	{
		$data = [
			'id'          => $this->id,
			'description' => $this->description,
			'type_id'     => $this->type_id,
			'parent_id'   => $this->parent_id,
			'wallet_id'   => $this->wallet_id,
		];

		return $data;
	}
}

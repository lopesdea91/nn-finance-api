<?php

namespace App\Http\Resources\Finance\Tag;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTagListResource extends JsonResource
{
	public function toArray($request)
	{
		$data = [
			'id' 					=> $this->id,
			'description' => $this->description,
			'type_id' 		=> $this->type->id,
			'wallet_id' 	=> $this->wallet->id,
			// "trashed"			=> (bool) $this->deleted_at
		];

		return $data;
	}
}

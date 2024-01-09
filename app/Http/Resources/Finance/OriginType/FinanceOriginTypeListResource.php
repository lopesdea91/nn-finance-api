<?php

namespace App\Http\Resources\Finance\OriginType;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOriginTypeListResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'description' => $this->description,
		];
	}
}

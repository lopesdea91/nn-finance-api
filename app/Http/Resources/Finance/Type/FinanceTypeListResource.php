<?php

namespace App\Http\Resources\Finance\Type;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTypeListResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'description' => $this->description,
		];
	}
}

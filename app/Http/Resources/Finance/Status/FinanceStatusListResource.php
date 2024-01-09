<?php

namespace App\Http\Resources\Finance\Status;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceStatusListResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'description' => $this->description,
		];
	}
}

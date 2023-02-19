<?php

namespace App\Http\Resources\Finance\OriginType;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOriginTypeResource extends JsonResource
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
			'description' => $this->description,
		];
	}
}

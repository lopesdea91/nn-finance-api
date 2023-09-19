<?php

namespace App\Http\Resources\Finance\Tag;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTagResource extends JsonResource
{

	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$type = !!$this->type
			? ['id' => $this->type->id, 'description' => $this->type->description]
			: null;

		$wallet = !!$this->wallet
			? ['id' => $this->wallet->id, 'description' => $this->wallet->description]
			: null;

		$data = [
			'id' 					=> $this->id,
			'description' => $this->description,
			'type' 				=> $type,
			'wallet' 			=> $wallet,
			"trashed"			=> !!$this->deleted_at
		];

		return $data;
	}
}

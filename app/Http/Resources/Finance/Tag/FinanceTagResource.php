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
		$type = $this->type ? ['id' => $this->type->id, 'description' => $this->type->description] : null;

		$wallet = $this->wallet ? ['id' => $this->wallet->id, 'description' => $this->wallet->description] : null;

		return [
			'id' 					=> $this->id,
			'description' => $this->description,
			'enable' 			=> $this->enable,
			'type' 				=> $type,
			'wallet' 			=> $wallet,
			"createdAt"  	=> $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
			"updatedAt"  	=> $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null
		];
	}
}

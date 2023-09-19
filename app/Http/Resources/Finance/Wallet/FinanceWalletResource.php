<?php

namespace App\Http\Resources\Finance\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceWalletResource extends JsonResource
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
			'id'            => $this->id,
			'description'   => $this->description,
			'panel'         => intval($this->panel),
			"trashed"				=> !!$this->deleted_at
		];
	}
}

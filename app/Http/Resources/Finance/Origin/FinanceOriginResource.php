<?php

namespace App\Http\Resources\Finance\Origin;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOriginResource extends JsonResource
{
	public function toArray($request)
	{
		$type = !!$this->type
			? ['id' => $this->type->id, 'description' => $this->type->description]
			: null;

		$parent = !!$this->parent
			? ['id' => $this->parent->id, 'description' => $this->parent->description]
			: null;

		$wallet = !!$this->wallet
			? ['id' => $this->wallet->id, 'description' => $this->wallet->description]
			: null;

		$data = [
			'id'          => $this->id,
			'description' => $this->description,
			// 'type_id'     => $this->type_id,
			'type'        => $type,
			// 'parent_id'   => $this->parent_id,
			'parent'      => $parent,
			// 'wallet_id'   => $this->wallet_id,
			'wallet'      => $wallet,
			"trashed"			=> (bool) $this->deleted_at
		];

		return $data;
	}
}

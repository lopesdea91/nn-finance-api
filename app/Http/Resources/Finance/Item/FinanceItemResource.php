<?php

namespace App\Http\Resources\Finance\Item;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceItemResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$obs = !!$this->obs
			? $this->obs->obs
			: '';

		$origin = !!$this->origin
			? ['id' => $this->origin->id, 'description' => $this->origin->description]
			: null;

		$status = !!$this->status
			? ['id' => $this->status->id, 'description' => $this->status->description]
			: null;

		$type = !!$this->type
			? ['id' => $this->type->id, 'description' => $this->type->description]
			: null;

		$tag_ids = $this->tags->map(function ($item) {
			return [
				'id' => $item->id,
				'description' => $item->description,
				'type_id' => $item->type_id,
			];
		});

		$wallet = $this->wallet
			? ['id' => $this->wallet->id, 'description' => $this->wallet->description]
			: null;

		$data = [
			"id"      => $this->id,
			"value"   => $this->value,
			"date"    => $this->date,
			"sort"    => intval($this->sort),
			"enable"  => intval($this->enable),
			"obs"     => $obs,
			"origin"  => $origin,
			"status"  => $status,
			"type"    => $type,
			"tag_ids" => $tag_ids,
			"wallet"  => $wallet,
		];

		!!$this->created_at && ($data['createdAt'] = $this->created_at->format('Y-m-d H:i:s'));
		!!$this->updated_at && ($data['updatedAt'] = $this->updated_at->format('Y-m-d H:i:s'));

		return $data;
	}
}

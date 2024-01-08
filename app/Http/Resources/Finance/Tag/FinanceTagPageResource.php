<?php

namespace App\Http\Resources\Finance\Tag;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTagPageResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'items' 		=> $this->map(fn ($item) => new FinanceTagResource($item)),
			'page' 			=> $this->currentPage(),
			'total' 		=> $this->total(),
			'limit' 		=> $this->perPage(),
			'last_page' => $this->lastPage(),
		];
	}
}

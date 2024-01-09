<?php

namespace App\Http\Resources\Finance\Wallet;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceWalletPageResource extends ResourceCollection
{
	public function toArray($request)
	{
		return [
			'items' => $this->map(fn ($item) => new FinanceWalletResource($item)),
			'page' => $this->currentPage(),
			'total' => $this->total(),
			'limit' => $this->perPage(),
			'last_page' => $this->lastPage(),
	];
	}
}

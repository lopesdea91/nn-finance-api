<?php

namespace App\Http\Resources\Finance\Wallet;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceWalletCollection extends ResourceCollection
{
	public $collects = 'App\Http\Resources\Finance\Wallet\FinanceWalletResource';

	/**
	 * Transform the resource collection into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return $this->resource;
	}
}

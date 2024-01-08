<?php

namespace App\Http\Resources\Finance\Origin;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOriginPageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'items' => $this->map(fn ($item) => new FinanceOriginResource($item)),
            'page' => $this->currentPage(),
            'total' => $this->total(),
            'limit' => $this->perPage(),
            'last_page' => $this->lastPage(),
        ];
    }
}

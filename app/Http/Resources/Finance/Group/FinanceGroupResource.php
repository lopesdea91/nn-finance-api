<?php

namespace App\Http\Resources\Finance\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $type = $this->type ? ['id' => $this->type->id, 'description' => $this->type->description,] : null;

        $wallet = $this->wallet ? ['id' => $this->wallet->id, 'description' => $this->wallet->description,] : null;

        return [
            'id'            => $this->id,
            'description'   => $this->description,
            'type_id'       => $this->type_id,
            'type'          => $type,
            'wallet_id'     => $this->wallet_id,
            'wallet'        => $wallet,
            // 'user_id'    => $this->user_id,
        ];
    }
}

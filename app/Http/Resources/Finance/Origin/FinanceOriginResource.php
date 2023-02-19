<?php

namespace App\Http\Resources\Finance\Origin;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceOriginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $type = $this->type
            ? [
                'id' => $this->type->id,
                'description' => $this->type->description,
            ] : null;

        $parent = $this->parent
            ? [
                'id' => $this->parent->id,
                'description' => $this->parent->description,
            ] : null;

        $wallet = $this->wallet
            ? [
                'id' => $this->wallet->id,
                'description' => $this->wallet->description,
            ] : null;

        return [
            'id'          => $this->id,
            'description' => $this->description,
            'enable'      => $this->enable,
            // 'type_id'     => $this->type_id,
            'type'        => $type,
            // 'parent_id'   => $this->parent_id,
            'parent'      => $parent,
            // 'wallet_id'   => $this->wallet_id,
            'wallet'      => $wallet,
        ];
    }
}

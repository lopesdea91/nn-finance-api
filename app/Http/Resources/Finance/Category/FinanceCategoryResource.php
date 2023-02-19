<?php

namespace App\Http\Resources\Finance\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $group = $this->group ? ['id' => $this->group->id, 'description' => $this->group->description, 'type_id' => $this->group->type_id,] : null;

        $type_id = $this->group ? $this->group->type->id : null;

        $type = $this->group ? ['id' => $this->group->type->id, 'description' => $this->group->type->description,] : null;

        $wallet = $this->wallet ? ['id' => $this->wallet->id, 'description' => $this->wallet->description,] : null;

        return [
            "id"            => $this->id,
            "description"   => $this->description,
            "enable"        => $this->enable,
            "obs"           => $this->obs,
            "group_id"      => $this->group_id,
            "group"         => $group,
            "type_id"       => $type_id,
            "type"          => $type,
            "wallet_id"     => $this->wallet_id,
            "wallet"        => $wallet,
        ];
    }
}

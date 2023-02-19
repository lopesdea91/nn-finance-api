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
        $obs = $this->obs ? $this->obs->obs : '';

        $origin = $this->origin ? ['id' => $this->origin->id, 'description' => $this->origin->description] : null;

        $status = $this->status ? ['id' => $this->status->id, 'description' => $this->status->description] : null;

        $tag_ids = $this->tags->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description,
                'type_id' => $item->type_id,
            ];
        });

        $type = $this->type ? ['id' => $this->type->id, 'description' => $this->type->description] : null;

        $wallet = $this->wallet ? ['id' => $this->wallet->id, 'description' => $this->wallet->description] : null;

        // $group = $this->group ? ['id' => $this->group->id, 'description' => $this->group->description] : null;

        // $category = $this->category ? ['id' => $this->category->id, 'description' => $this->category->description] : null;

        return [
            "id"          => $this->id,
            "value"       => $this->value,
            "date"        => $this->date,
            "sort"        => $this->sort,
            "enable"      => $this->enable,
            "obs"         => $obs,
            // "origin_id"   => $this->origin_id,
            "origin"      => $origin,
            // "status_id"   => $this->status_id,
            "status"      => $status,
            // "type_id"     => $this->type_id,
            "type"        => $type,
            "tag_ids"    => $tag_ids,
            // "category_id" => $this->category_id,
            // "category"    => $category,
            // "group_id"    => $this->group_id,
            // "group"       => $group,
            // "wallet_id"   => $this->wallet_id,
            "wallet"      => $wallet,
            "createdAt"  => $this->created_at->format('Y-m-d H:i:s'),
            "updatedAt"  => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}

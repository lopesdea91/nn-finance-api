<?php

use App\Models\FinanceItemModel;
use Illuminate\Support\Facades\Route;

Route::get('teste', function () {
  //     // $res = FinanceItemModel::select('*')->
  //     // ->get();
  //     //   ->where([
  //     //     'id'      => $id,
  //     //     'user_id' => $user_id,
  //     //   ]);

  // FinanceItemRepository::store([
  //     'value'       =>  1.2,
  //     'date'        => '2023-01-15',
  //     'sort'        => 1,
  //     'enable'      => 1,
  //     'origin_id'   => 1,
  //     'status_id'   => 1,
  //     'type_id'     => 2,
  //     'wallet_id'   => 1,
  // ]);


  ## create
  // $items = FinanceItemModel::create([
  //     'value'       =>  50,
  //     'date'        => '2023-01-15',
  //     'sort'        => 1,
  //     'enable'      => 1,
  //     'origin_id'   => 1,
  //     'status_id'   => 1,
  //     'type_id'     => 1,
  //     'wallet_id'   => 1,
  // ]);
  // $items->obs()->create([
  //     'obs' => 'teste obs',
  //     'item_id' => $items->id
  // ]);
  // $items->tags()->sync([1]);

  $items = FinanceItemModel::with(['wallet', 'origin',  'type', 'status', 'obs', 'tags'])->find(8);
  $items->obs()->update([
    'obs' => 'teste obs 123',
  ]);
  $items->tags()->sync([1, 2, 3]);


  ## query
  // $items = FinanceItemModel::with([
  //     'wallet' => function ($q) {
  //         $q->select('id');
  //     },
  //     'type' => function ($q) {
  //         $q->select('id', 'description');
  //     },
  //     'status' => function ($q) {
  //         $q->select('id', 'description');
  //     },
  //     'origin' => function ($q) {
  //         $q->select('id', 'description');
  //     },
  //     'tags' => function ($q) {
  //         $q->select('finance_tag.id', 'finance_tag.description');
  //     },
  // ])
  //     // ->where('id', '=', 1) // para o model em uso
  //     ->whereRelation('wallet', function ($q) {
  //         // $q->where('id', '=', 1); // para o relacionamento
  //     })
  //     ->whereHas('tags', function ($q) {
  //         $q->whereIn('tag_id', [3]); // para o pivot
  //     })
  //     ->get()
  //     ->toArray();

  //     (new FinanceItemService)->store([
  //         "value" => "3",
  //         "date" => "2023-01-02",
  //         "obs" => "lanche",
  //         "sort" => 1,
  //         "enable" => 1,
  //         "origin_id" => 1,
  //         "status_id" => 1,
  //         "type_id" => 2,
  //         "tags_ids" => [1, 2, 3],
  //         "wallet_id" => 3,
  //         "repeat" => "UNIQUE",
  //         "repeat_options" => [
  //             "for_times" => 0,
  //             "until_month" => "2023-04"
  //         ]
  //     ]);
});

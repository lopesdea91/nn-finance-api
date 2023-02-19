<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\FinanceItemRepeatModel;

class FinanceItemRepeatRepository
{
  // static function paginate(Request $request)
  // {
  //   $user_id = Auth::user()->id;
  //   $order  = 'id';
  //   $sort   = 'desc';
  //   $limit = 15;
  //   $relations = ['wallet', 'group', 'category', 'type', 'status', 'origin'];
  //   $where = [];

  //   $query = $request->query();

  //   $model = FinanceItemModel::with($relations);

  //   # WHERE 
  //   $where['user_id'] = $user_id;

  //   if (key_exists('_q',          $query))  $where[] = ['obs',         'like', "%{$query['_q']}%"];
  //   if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
  //   if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];
  //   if (key_exists('group_id',    $query))  $where[] = ['group_id',    '=',    $query['group_id']];
  //   if (key_exists('category_id', $query))  $where[] = ['category_id', '=',    $query['category_id']];
  //   if (key_exists('type_id',     $query))  $where[] = ['type_id',     '=',    $query['type_id']];
  //   if (key_exists('status_id',   $query))  $where[] = ['status_id',   '=',    $query['status_id']];
  //   if (key_exists('origin_id',   $query))  $where[] = ['origin_id',   '=',    $query['origin_id']];
  //   if (key_exists('user_id',     $query))  $where[] = ['user_id',     '=',    $query['user_id']];

  //   # order
  //   if (key_exists('_order', $query)) {
  //     $order = $query['_order'];

  //     if ($order === 'updated') $order = 'updated_at';
  //     if ($order === 'created') $order = 'created_at';

  //     $columns = Schema::getColumnListing('api_crm_finance_group');

  //     if (!in_array($order, $columns))  $order = 'id';

  //     # sort
  //     if (key_exists('_sort', $query)) {
  //       $sort    = $query['_sort'];

  //       $options = ['asc', 'desc'];

  //       if (in_array($sort, $options))  $order = "{$order} $sort";
  //     }
  //   }

  //   $model->where($where)->orderByRaw($order);

  //   # PAGINATE
  //   if (key_exists('_limit', $query)) $limit = $query['_limit'];


  //   return $model->paginate($limit);
  // }
  // static function all(Request $request)
  // {
  //   $user_id   = Auth::user()->id;
  //   $relations = ['wallet', 'group', 'category', 'type', 'status', 'origin'];

  //   $query = $request->query();

  //   # WHERE 
  //   $where = [];
  //   $where['user_id'] = $user_id;

  //   if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
  //   if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];
  //   if (key_exists('group_id',    $query))  $where[] = ['group_id',    '=',    $query['group_id']];
  //   if (key_exists('category_id', $query))  $where[] = ['category_id', '=',    $query['category_id']];
  //   if (key_exists('type_id',     $query))  $where[] = ['type_id',     '=',    $query['type_id']];
  //   if (key_exists('status_id',   $query))  $where[] = ['status_id',   '=',    $query['status_id']];
  //   if (key_exists('origin_id',   $query))  $where[] = ['origin_id',   '=',    $query['origin_id']];
  //   if (key_exists('user_id',     $query))  $where[] = ['user_id',     '=',    $query['user_id']];

  //   return FinanceItemModel::with($relations)->where($where)->get();
  // }
  // static function id($id)
  // {
  //   $user_id = Auth::user()->id;

  //   return FinanceItemModel::where([
  //     'id'      => $id,
  //     'user_id' => $user_id,
  //   ])->first();
  // }
  static function store($fields)
  {
    return FinanceItemRepeatModel::create([
      'item_id'       => $fields['item_id'],
      'repeat'        => $fields['repeat'],
    ]);
  }
  static function delete($id)
  {
    return FinanceItemRepeatModel::where([
      'item_id'       => $id,
    ])->delete();
  }
}

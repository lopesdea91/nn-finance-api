<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\FinanceGroupModel;

class FinanceGroupRepository
{
    static function paginate(Request $request)
    {
        $user_id = Auth::user()->id;
        $order  = 'id';
        $sort   = 'desc';
        $limit = 15;
        $relations = ['type', 'wallet'];
        $where = [];

        $query = $request->query();

        $model = FinanceGroupModel::with($relations);

        # WHERE 
        $where['user_id'] = $user_id;

        if (key_exists('_q',          $query))  $where[] = ['description', 'like', "%{$query['_q']}%"];
        if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
        if (key_exists('type_id',     $query))  $where[] = ['type_id',     '=',    $query['type_id']];
        if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];
        if (key_exists('user_id',     $query))  $where[] = ['user_id',     '=',    $query['user_id']];

        # order
        if (key_exists('_order', $query)) {
            $order = $query['_order'];

            if ($order === 'updated') $order = 'updated_at';
            if ($order === 'created') $order = 'created_at';

            $columns = Schema::getColumnListing('api_crm_finance_group');

            if (!in_array($order, $columns))  $order = 'id';

            # sort
            if (key_exists('_sort', $query)) {
                $sort    = $query['_sort'];

                $options = ['asc', 'desc'];

                if (in_array($sort, $options))  $order = "{$order} $sort";
            }
        }

        $model->where($where)->orderByRaw($order);

        # PAGINATE
        if (key_exists('_limit', $query)) $limit = $query['_limit'];


        return $model->paginate($limit);
    }
    static function all(Request $request)
    {
        $user_id   = Auth::user()->id;
        $relations = ['type', 'wallet'];

        $query = $request->query();

        # WHERE 
        $where = [];
        $where['user_id'] = $user_id;

        if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
        if (key_exists('type_id',     $query))  $where[] = ['type_id',     '=',    $query['type_id']];
        if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];

        return FinanceGroupModel::with($relations)->where($where)->get();
    }
    static function id($id)
    {
        $user_id = Auth::user()->id;

        return FinanceGroupModel::where([
            'id'      => $id,
            'user_id' => $user_id,
        ])->first();
    }
    static function store($fields)
    {
        $user_id = Auth::user()->id;

        return FinanceGroupModel::create([
            'user_id'     => $user_id,
            'description' => $fields['description'],
            'enable'      => $fields['enable'],
            'type_id'     => $fields['type_id'],
            'wallet_id'   => $fields['wallet_id'],
        ]);
    }
    static function update($id, $fields)
    {
        $where = [
            'id'      => $id,
            'user_id' => Auth::user()->id,
        ];

        $updateField = [
            'description' => $fields['description'],
            'enable'      => $fields['enable'],
            'type_id'     => $fields['type_id'],
            'wallet_id'   => $fields['wallet_id'],
        ];

        return FinanceGroupModel::where($where)->first()->update($updateField);
    }
    static function delete($id)
    {
        $user_id = Auth::user()->id;

        return FinanceGroupModel::where(array_merge(
            [
                'id'      => $id,
                'user_id' => $user_id,
            ],
        ))->delete();
    }
}

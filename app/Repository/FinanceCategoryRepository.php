<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\FinanceCategoryModel;

class FinanceCategoryRepository
{
    static function paginate(Request $request)
    {
        $user_id = Auth::user()->id;
        $order  = 'id';
        $sort   = 'desc';
        $limit = 15;
        $relations = ['group', 'wallet'];
        $where = [];
        $whereGroup = [];

        $query = $request->query();

        $model = FinanceCategoryModel::with($relations);

        # WHERE 
        $where['user_id'] = $user_id;

        if (key_exists('_q',          $query))  $where[] = ['description',  'like', "%{$query['_q']}%"];
        if (key_exists('enable',      $query))  $where[] = ['enable',       '=',    $query['enable']];
        if (key_exists('group_id',    $query))  $where[] = ['group_id',     '=',    $query['group_id']];
        if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',    '=',    $query['wallet_id']];
        if (key_exists('user_id',     $query))  $where[] = ['user_id',      '=',    $query['user_id']];
        if (key_exists('type_id',     $query))  $whereGroup[] = ['type_id', '=',    $query['type_id']];

        # order
        if (key_exists('_order', $query)) {
            $order = $query['_order'];

            if ($order === 'updated') $order = 'updated_at';
            if ($order === 'created') $order = 'created_at';

            $columns = Schema::getColumnListing('finance_category');

            if (!in_array($order, $columns))  $order = 'id';

            # sort
            if (key_exists('_sort', $query)) {
                $sort    = $query['_sort'];

                $options = ['asc', 'desc'];

                if (in_array($sort, $options))  $order = "{$order} $sort";
            }
        }

        $model->where($where)
            ->whereHas('group', function ($query) use ($whereGroup) {
                if (count($whereGroup))
                    $query->where($whereGroup);
            })
            ->orderByRaw($order);

        # PAGINATE
        if (key_exists('_limit', $query)) $limit = $query['_limit'];


        return $model->paginate($limit);
    }
    static function all(Request $request)
    {
        $user_id = Auth::user()->id;
        $relations = ['group', 'wallet'];

        $query = $request->query();

        # WHERE 
        $where = [];
        $where['user_id'] = $user_id;

        if (key_exists('enable',      $query))  $where[] = ['enable',       '=',    $query['enable']];
        if (key_exists('group_id',    $query))  $where[] = ['group_id',     '=',    $query['group_id']];
        if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',    '=',    $query['wallet_id']];
        if (key_exists('user_id',     $query))  $where[] = ['user_id',      '=',    $query['user_id']];
        if (key_exists('type_id',     $query))  $whereGroup[] = ['type_id', '=',    $query['type_id']];

        return FinanceCategoryModel::with($relations)->where($where)->get();
    }
    static function id($id)
    {
        $user_id = Auth::user()->id;
        $relations = ['group', 'group.type', 'wallet'];

        return FinanceCategoryModel::with($relations)
            ->where([
                'id' => $id,
                'user_id' => $user_id,
            ])->first();
    }
    static function store($fields)
    {
        $user_id = Auth::user()->id;

        return FinanceCategoryModel::create([
            'user_id'     => $user_id,
            'description' => $fields['description'],
            'enable'      => $fields['enable'],
            'obs'         => $fields['obs'],
            'group_id'    => $fields['group_id'],
            'wallet_id'   => $fields['wallet_id'],
        ]);
    }
    static function update($id, $fields)
    {
        $where = [
            'id'      => $id,
            'user_id' => Auth::user()->id
        ];

        $updateField = [
            'description' => $fields['description'],
            'enable'      => $fields['enable'],
            'obs'         => $fields['obs'],
            'group_id'    => $fields['group_id'],
            'wallet_id'   => $fields['wallet_id'],
        ];

        return FinanceCategoryModel::where($where)->first()->update($updateField);
    }
    static function delete($id)
    {
        $user_id = Auth::user()->id;

        return FinanceCategoryModel::where(array_merge(
            [
                'id'      => $id,
                'user_id' => $user_id,
            ],
        ))->delete();
    }
}

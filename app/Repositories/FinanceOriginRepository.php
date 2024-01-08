<?php

namespace App\Repositories;

use App\Models\FinanceOriginModel;
use Illuminate\Support\Facades\Auth;

class FinanceOriginRepository
{
  private $financeOriginModel;

  public function __construct(FinanceOriginModel $financeOriginModel)
  {
    $this->financeOriginModel = $financeOriginModel;
  }

  public function get($search)
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])->get();
  }
  public function getById($id, $search)
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])->find($id);
  }
  public function getPage($search)
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])
    ->orderByRaw(
      "{$search['filter']['_order']} {$search['filter']['_sort']}"
    )
    ->paginate(
      (int) $search['filter']['_limit'],
      ['*'],
      '_page',
      (int) $search['filter']['_page']
    );
  }
  public function create($fields)
  {
    return $this->financeOriginModel->create([
      'description' => $fields['description'],
      'type_id'     => $fields['type_id'],
      'parent_id'   => $fields['parent_id'] ?? null,
      'wallet_id'   => $fields['wallet_id']
    ]);
  }
  public function update($id, $fields)
  {
    $item = $this->financeOriginModel->find($id);
    $item->fill([
      'description' => $fields['description'],
      'type_id'     => $fields['type_id'],
      'parent_id'   => $fields['parent_id'] ?? null,
      'wallet_id'   => $fields['wallet_id']
    ]);
    return $item->save();
  }
  public function delete()
  {
  }
  // others
  public function has($id)
  {
    return (bool) $this->financeOriginModel->withTrashed()->select('id')->find($id);
  }
  public function findDelete($id)
  {
    return $this->financeOriginModel->withTrashed()->select('id', 'deleted_at')->find($id);
  }
  public function findRestore($id)
  {
    return $this->financeOriginModel->withTrashed()->select('id')->find($id);
  }
  // privates
  private function query($search)
  {
    $model = !!key_exists('deleted_at', $search)
      ? $this->financeOriginModel->onlyTrashed()
      : $this->financeOriginModel->withTrashed(false);

    return $model
      ->with([
        'type',
        'parent',
        'wallet',
        // 'wallet' => function ($q) {
        //   $q->where('user_id', Auth::user()->id);
        // }
      ])
      ->when(key_exists('description', $search), function ($query) use ($search) {
        return $query->where('description',  'like', "%{$search['description']}%");
      })
      ->when(key_exists('type_id', $search), function ($query) use ($search) {
        return $query->whereHas('type', function ($query) use ($search) {
          return $query->where('type_id', $search['type_id']);
        });
      })
      ->when(key_exists('parent_id', $search), function ($query) use ($search) {
        return $query->whereHas('parent', function ($query) use ($search) {
          return $query->where('parent_id', $search['parent_id']);
        });
      })
      ->when(key_exists('wallet_id', $search), function ($query) use ($search) {
        return $query->whereHas('wallet', function ($query) use ($search) {
          return $query->where('wallet_id', $search['wallet_id']);
        });
      })
      ->when(key_exists('user_id', $search), function ($query) use ($search) {
        return $query->whereHas('wallet', function ($query) use ($search) {
          return $query->where('user_id', $search['user_id']);
        });
      });
  }
  private function formatSearch($search)
  {
    return [
      'filter' => array_filter([
        '_sort'       => $search['_sort']     ?? 'asc',
        '_order'      => $search['_order']    ?? 'description',
        '_limit'      => $search['_limit']    ?? '15',
        '_page'       => $search['_page']     ?? '1',
      ]), // 'ucfirst')
      'fields' => array_filter([
        'description' => $search['_q']        ?? null,
        'deleted_at'  => $search['_trashed']  ?? null,
        'type_id'     => $search['type_id']   ?? null,
        'parent_id'   => $search['parent_id'] ?? null,
        'wallet_id'   => $search['wallet_id'] ?? null,
        'user_id'     => $search['user_id']   ?? Auth::user()->id,
      ]) // 'ucfirst')
    ];
  }
}

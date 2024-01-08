<?php

namespace App\Repositories;

use App\Models\FinanceTagModel;
use Illuminate\Support\Facades\Auth;

class FinanceTagRepository
{
  private $financeTagModel;

  public function __construct(FinanceTagModel $financeTagModel)
  {
    $this->financeTagModel = $financeTagModel;
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
    return $this->financeTagModel->create([
      'description' => $fields['description'],
      'type_id'     => $fields['type_id'],
      'wallet_id'   => $fields['wallet_id']
    ]);
  }
  public function update($id, $fields)
  {
    $item = $this->financeTagModel->find($id);
    $item->fill([
      'description' => $fields['description'],
      'type_id'     => $fields['type_id'],
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
    return (bool) $this->financeTagModel->withTrashed()->select('id')->find($id);
  }
  public function findDelete($id)
  {
    return $this->financeTagModel->withTrashed()->select('id', 'deleted_at')->find($id);
  }
  public function findRestore($id)
  {
    return $this->financeTagModel->withTrashed()->select('id')->find($id);
  }
  // privates
  private function query($search)
  {
    $model = !!key_exists('deleted_at', $search)
      ? $this->financeTagModel->onlyTrashed()
      : $this->financeTagModel->withTrashed(false);

    return $model
      ->with([
        'type',
        'wallet'
      ])
      ->when(key_exists('description', $search), function ($query) use ($search) {
        return $query->where('description',  'like', "%{$search['description']}%");
      })
      ->when(key_exists('type_id', $search), function ($query) use ($search) {
        return $query->whereHas('type', function ($query) use ($search) {
          return $query->where('type_id', $search['type_id']);
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
        'wallet_id'   => $search['wallet_id'] ?? null,
        'user_id'     => $search['user_id']   ?? Auth::user()->id,
      ]) // 'ucfirst')
    ];
  }
}

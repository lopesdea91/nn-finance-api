<?php

namespace App\Repositories;

use App\Models\FinanceWalletModel;
use Illuminate\Support\Facades\Auth;

class FinanceWalletRepository
{
  private $financeWalletModel;

  public function __construct(FinanceWalletModel $financeWalletModel)
  {
    $this->financeWalletModel = $financeWalletModel;
  }

  public function get($search = [])
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])->get();
  }
  public function getById($id, $search)
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])->find($id);
  }
  public function getPage($search = [])
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
    return $this->financeWalletModel->create([
      'description' => $fields['description'],
      'user_id' => Auth::user()->id,
    ]);
  }
  public function update($id, $fields)
  {
    $item = $this->financeWalletModel->find($id);
    $item->fill([
      'description' => $fields['description'],
      'panel'       => $fields['panel'],
    ]);
    return $item->save();
  }
  public function delete()
  {
  }
  // others
  public function has($id)
  {
    return (bool) $this->financeWalletModel->withTrashed()->select('id')->find($id);
  }
  public function findDelete($id)
  {
    return $this->financeWalletModel->withTrashed()->select('id', 'deleted_at')->find($id);
  }
  public function findRestore($id)
  {
    return $this->financeWalletModel->withTrashed()->select('id')->find($id);
  }
  // privates
  private function query($search)
  {
    $model = !!key_exists('deleted_at', $search)
      ? $this->financeWalletModel->onlyTrashed()
      : $this->financeWalletModel->withTrashed(false);

    return $model
      // ->with([
      //   'type',
      //   'parent',
      //   'wallet',
      // ])
      ->when(key_exists('description', $search), function ($query) use ($search) {
        return $query->where('description',  'like', "%{$search['description']}%");
      })
      ->when(key_exists('user_id', $search), function ($query) use ($search) {
        return $query->where('user_id', $search['user_id']);
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
        'user_id'     => $search['user_id']   ?? Auth::user()->id,
      ]) // 'ucfirst')
    ];
  }
}

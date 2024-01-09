<?php

namespace App\Repositories;

use App\Models\FinanceWalletCompositionModel;
use Illuminate\Support\Facades\Auth;

class FinanceWalletCompositionRepository
{
  private $financeWalletCompositionModel;

  public function __construct(FinanceWalletCompositionModel $financeWalletCompositionModel)
  {
    $this->financeWalletCompositionModel = $financeWalletCompositionModel;
  }

  public function get($search)
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])->get();
  }
  // public function getById($id, $search)
  // {
  //   $search = $this->formatSearch($search);

  //   return $this->query($search['fields'])->find($id);
  // }
  // public function getPage($search)
  // {
  //   $search = $this->formatSearch($search);

  //   return $this->query($search['fields'])
  //   ->orderByRaw(
  //     "{$search['filter']['_order']} {$search['filter']['_sort']}"
  //   )
  //   ->paginate(
  //     (int) $search['filter']['_limit'],
  //     ['*'],
  //     '_page',
  //     (int) $search['filter']['_page']
  //   );
  // }
  public function create($fields)
  {
    return $this->financeWalletCompositionModel->create([
      'percentage_limit' 	=> $fields['percentage_limit'],
      'tag_id' 						=> $fields['tag_id'],
      'wallet_id' 				=> $fields['wallet_id'],
    ]);
  }
  // public function update($id, $fields)
  // {
  //   $item = $this->financeWalletCompositionModel->find($id);
  //   $item->fill([
  //     'description' => $fields['description'],
  //     'wallet_id'   => $fields['wallet_id']
  //   ]);
  //   return $item->save();
  // }
  // // others
  // public function has($id)
  // {
  //   return (bool) $this->financeWalletCompositionModel->withTrashed()->select('id')->find($id);
  // }
  // public function findDelete($id)
  // {
  //   return $this->financeWalletCompositionModel->withTrashed()->select('id', 'deleted_at')->find($id);
  // }
  // public function findRestore($id)
  // {
  //   return $this->financeWalletCompositionModel->withTrashed()->select('id')->find($id);
  // }
  // // privates
  public function query($search)
  {
    return $this->financeWalletCompositionModel
      ->with([
        'tag',
        'wallet'
      ])
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
        // '_sort'       => $search['_sort']     ?? 'asc',
        // '_order'      => $search['_order']    ?? 'description',
        // '_limit'      => $search['_limit']    ?? '15',
        // '_page'       => $search['_page']     ?? '1',
      ]), // 'ucfirst')
      'fields' => array_filter([
        // 'description' => $search['_q']        ?? null,
        // 'deleted_at'  => $search['_trashed']  ?? null,
        'wallet_id'   => $search['wallet_id'] ?? null,
        'user_id'     => $search['user_id']   ?? Auth::user()->id,
      ]) // 'ucfirst')
    ];
  }
}

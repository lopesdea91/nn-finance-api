<?php

namespace App\Repositories;

use App\Models\FinanceOriginTypeModel;

class FinanceOriginTypeRepository
{
  private $financeOriginTypeModel;

  public function __construct(FinanceOriginTypeModel $financeOriginTypeModel)
  {
    $this->financeOriginTypeModel = $financeOriginTypeModel;
  }

  public function get($search = [])
  {
    $search = $this->formatSearch($search);

    return $this->query($search['fields'])->get();
  }

  // privates
  private function query($search)
  {
    return $this->financeOriginTypeModel
      ->when(key_exists('description', $search), function ($query) use ($search) {
        return $query->where('description',  'like', "%{$search['description']}%");
      });
  }
  private function formatSearch($search)
  {
    return [
      // 'filter' => array_filter([
      // '_sort'       => $search['_sort']     ?? 'asc',
      // '_order'      => $search['_order']    ?? 'description',
      // '_limit'      => $search['_limit']    ?? '15',
      // '_page'       => $search['_page']     ?? '1',
      // ]), // 'ucfirst')
      'fields' => array_filter([
        'description' => $search['_q']        ?? null,
      ]) // 'ucfirst')
    ];
  }
}

<?php

namespace App\Repository;

use App\Models\FinanceItemModel;
use App\Repository\Base\CrudRepository;

class FinanceItemRepository extends CrudRepository
{
  protected $model;
  protected $fields = ['id', 'value', 'date', 'sort', 'enable', 'origin_id', 'status_id', 'type_id', 'wallet_id', 'created_at', 'updated_at'];
  protected $relationships = [
    'wallet',
    'origin',
    'type',
    'status',
    'obs',
    'tags'
  ];
  private $currentPeriod;
  private $dateMaxCurrentPeriod = false;

  public function __construct()
  {
    $this->model = new FinanceItemModel;
  }

  public function queryModel($model)
  {
    // ultimo dia do mês em relação ao currentPeriod
    if ($this->dateMaxCurrentPeriod) {
      $model->where('date', '<=', $this->currentPeriod->endOfMonth()->format('Y-m-d'));
    }
    return $model;
  }

  public function paginate($args)
  {
    $query =    key_exists('query', $args)    ? $args['query']    : [];
    $where =    key_exists('where', $args)    ? $args['where']    : [];
    $whereHas = key_exists('whereHas', $args) ? $args['whereHas'] : [];

    # WHERE 
    if (key_exists('enable',      $query))  $where[] = ['enable',      '=',    $query['enable']];
    if (key_exists('wallet_id',   $query))  $where[] = ['wallet_id',   '=',    $query['wallet_id']];
    if (key_exists('group_id',    $query))  $where[] = ['group_id',    '=',    $query['group_id']];
    if (key_exists('category_id', $query))  $where[] = ['category_id', '=',    $query['category_id']];
    if (key_exists('type_id',     $query))  $where[] = ['type_id',     '=',    $query['type_id']];
    if (key_exists('status_id',   $query))  $where[] = ['status_id',   '=',    $query['status_id']];
    if (key_exists('origin_id',   $query))  $where[] = ['origin_id',   '=',    $query['origin_id']];
    if (key_exists('user_id',     $query))  $where[] = ['user_id',     '=',    $query['user_id']];

    # extract type_preveiw
    if (key_exists('period', $query)) {
      $p = $query['period'];
      $explode_period = explode('-', $p);

      $this->currentPeriod  = now()->setDate($explode_period[0], $explode_period[1], 01);

      $query['whereYear']   = $this->currentPeriod->format('Y');
      $query['whereMonth']  = $this->currentPeriod->format('m');

      unset($query['period'], $p, $explode_period);
    }

    # extract type_preveiw
    if (key_exists('type_preveiw', $query)) {
      $type_preveiw = $query['type_preveiw'];

      $order = 'date';
      $sort  = 'desc';

      if ($type_preveiw === 'extract') {
        $order = 'date';
        $sort  = 'desc';
      }
      if ($type_preveiw === 'historic') {
        $order = 'id';
        $sort  = 'desc';
      }
      if ($type_preveiw === 'moviment') {
        $order = 'date';
        $sort  = 'desc';
      }

      $query['_order'] = "{$order} $sort";

      // quando for /historic ou /moviment não faz query com period
      if (in_array($type_preveiw, ['historic', 'moviment'])) {
        $this->dateMaxCurrentPeriod = true;

        unset($query['whereYear']);
        unset($query['whereMonth']);
      }

      unset($query['type_preveiw'], $type_preveiw);
    }

    if (key_exists('_q', $query)) {
      $content = $query['_q'];

      $whereHas['obs'] = function ($q) use ($content) {
        $q->where('obs', 'like', "%{$content}%");
      };

      unset($query['_q'], $content);
    }

    unset($query['_paginate']);

    return parent::paginate([
      'query'     => $query,
      'where'     => $where,
      'whereHas'  => $whereHas,
    ]);
  }
}

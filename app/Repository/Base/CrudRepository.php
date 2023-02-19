<?php

namespace App\Repository\Base;

use Illuminate\Support\Facades\Schema;

abstract class CrudRepository
{
  protected $model;
  protected $fields = ['*'];
  protected $relationships = [];
  protected $queryModel;

  public function queryModel($model)
  {
    return $model;
  }

  public function paginate($args)
  {
    $query =    key_exists('query', $args)    ? $args['query']    : [];
    $where =    key_exists('where', $args)    ? $args['where']    : [];
    $whereHas = key_exists('whereHas', $args) ? $args['whereHas'] : [];

    $order  = 'id';
    $sort   = 'desc';
    $limit = 15;

    $model = $this->model::with($this->relationships);

    # order
    if (key_exists('_order', $query)) {
      $order = $query['_order'];

      if ($order === 'updated') $order = 'updated_at';
      if ($order === 'created') $order = 'created_at';

      // $columns = Schema::getColumnListing('finance_wallet');

      // if (!in_array($order, $columns))  $order = 'id';

      # sort
      if (key_exists('_sort', $query)) {
        $sort    = $query['_sort'];

        $options = ['asc', 'desc'];

        if (in_array($sort, $options))  $order = "{$order} $sort";
      }
    }

    if (key_exists('whereYear', $query)) {
      // date contains -> Y
      $model->whereYear('date', $query['whereYear']);
    }

    if (key_exists('whereMonth', $query)) {
      // date contains -> M
      $model->whereMonth('date', $query['whereMonth']);
    }

    $model = $this->queryModel($model);

    $model->where($where)->orderByRaw($order);

    foreach ($whereHas as $key => $has) {
      $model->whereHas($key, $has);
    }

    # PAGINATE
    if (key_exists('_limit', $query)) $limit = $query['_limit'];

    return $model->paginate($limit, $this->fields);
  }

  public function all($args = [])
  {
    // $query =    key_exists('query', $args)    ? $args['query']    : [];
    $where =    key_exists('where', $args)    ? $args['where']    : [];
    $whereHas = key_exists('whereHas', $args) ? $args['whereHas'] : [];

    $this->model = $this->model::select($this->fields)->with($this->relationships)->where($where);

    foreach ($whereHas as $key => $has) {
      $this->model = $this->model->whereHas($key, $has);
    }
    return $this->model;
  }

  public function id($id)
  {
    return $this->model::with($this->relationships)->find($id, $this->fields);
  }

  public function create($fields)
  {
    return $this->model::create($fields);
  }

  public function update($where, $updateField)
  {
    $search = $this->model::where($where)->first();

    if (!!$search) {
      $search->update($updateField);
    }

    return $search;
  }

  public function delete($id)
  {
    return $this->model::find($id)->delete();
  }

  public function existId($id)
  {
    return !!$this->model::find($id, ['id']);
  }
}

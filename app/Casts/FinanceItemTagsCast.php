<?php

namespace App\Casts;

use App\Models\FinanceTagsModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class FinanceItemTagsCast implements CastsAttributes
{
  public function get($model, $key, $value, $attributes)
  {
    $ids = json_decode($value);

    return FinanceTagsModel::whereIn('id', $ids)->get();
    // dd(
    //   $model,
    //   $key,
    //   $value,
    //   $attributes
    // );

    // return json_decode($value); //'teste get';
  }

  public function set($model, $key, $value, $attributes)
  {
    return [
      $key => json_encode($value)
    ];
  }
}

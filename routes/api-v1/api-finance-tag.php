<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceTagController as FinanceTagControllerV1;

Route::prefix('tag')->group(function () {
  Route::get('/{id}/enabled', [FinanceTagControllerV1::class, 'enabled']);
  Route::get('/{id}/disabled', [FinanceTagControllerV1::class, 'disabled']);

  Route::get('', [FinanceTagControllerV1::class, 'all']);
  Route::get('/{id}', [FinanceTagControllerV1::class, 'id']);
  Route::post('', [FinanceTagControllerV1::class, 'store']);
  Route::put('/{id}', [FinanceTagControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceTagControllerV1::class, 'delete']);
});

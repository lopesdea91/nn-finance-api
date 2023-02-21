<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceItemController as FinanceItemControllerV1;

Route::prefix('item')->group(function () {
  Route::get('/{id}/status/{statusId}', [FinanceItemControllerV1::class, 'status']);

  Route::get('/{id}/enabled', [FinanceItemControllerV1::class, 'enabled']);
  Route::get('/{id}/disabled', [FinanceItemControllerV1::class, 'disabled']);

  Route::get('', [FinanceItemControllerV1::class, 'all']);
  Route::get('/{id}', [FinanceItemControllerV1::class, 'id']);
  Route::post('', [FinanceItemControllerV1::class, 'storeItem']);
  Route::put('/{id}', [FinanceItemControllerV1::class, 'updateItem']);
  Route::delete('/{id}', [FinanceItemControllerV1::class, 'deleteItem']);
});

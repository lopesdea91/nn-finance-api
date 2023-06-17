<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceItemController as FinanceItemControllerV1;

Route::prefix('item')->group(function () {
  Route::get('', [FinanceItemControllerV1::class, 'all']);
  Route::get('/{id}', [FinanceItemControllerV1::class, 'id']);
  Route::post('', [FinanceItemControllerV1::class, 'store']);
  Route::put('/{id}', [FinanceItemControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceItemControllerV1::class, 'delete']);
});

Route::post('item-status', [FinanceItemControllerV1::class, 'status']);
Route::get('item-enabled/{id}', [FinanceItemControllerV1::class, 'enabled']);
Route::get('item-disabled/{id}', [FinanceItemControllerV1::class, 'disabled']);

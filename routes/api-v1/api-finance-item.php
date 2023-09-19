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

Route::get('item-restore/{id}', [FinanceItemControllerV1::class, 'restore']);
Route::post('item-status', [FinanceItemControllerV1::class, 'status']);

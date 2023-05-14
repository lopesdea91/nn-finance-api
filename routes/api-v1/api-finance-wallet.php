<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletController as FinanceWalletControllerV1;

Route::prefix('wallet')->group(function () {
  Route::post('/{id}/composition', [FinanceWalletControllerV1::class, 'composition']);
  Route::get('periods-data', [FinanceWalletControllerV1::class, 'getPeriodsData']);
  Route::get('consolidate-month', [FinanceWalletControllerV1::class, 'getConsolidateMonth']);
  Route::post('consolidate-month', [FinanceWalletControllerV1::class, 'processConsolidateMonth']);

  Route::get('/{id}/enabled', [FinanceWalletControllerV1::class, 'enabled']);
  Route::get('/{id}/disabled', [FinanceWalletControllerV1::class, 'disabled']);

  Route::get('', [FinanceWalletControllerV1::class, 'all']);
  Route::get('/{id}', [FinanceWalletControllerV1::class, 'id']);
  Route::post('', [FinanceWalletControllerV1::class, 'store']);
  Route::put('/{id}', [FinanceWalletControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceWalletControllerV1::class, 'delete']);
});

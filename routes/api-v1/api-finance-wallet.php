<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletController as FinanceWalletControllerV1;

Route::prefix('wallet')->group(function () {
  Route::get('', [FinanceWalletControllerV1::class, 'all']);
  Route::get('/{id}', [FinanceWalletControllerV1::class, 'id']);
  Route::post('', [FinanceWalletControllerV1::class, 'store']);
  Route::put('/{id}', [FinanceWalletControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceWalletControllerV1::class, 'delete']);
});

Route::get('wallet-enabled/{id}', [FinanceWalletControllerV1::class, 'enabled']);
Route::get('wallet-disabled/{id}', [FinanceWalletControllerV1::class, 'disabled']);

# composition
Route::post('wallet-composition/{id}', [FinanceWalletControllerV1::class, 'composition']);

# periods
Route::get('wallet-dataPeriods', [FinanceWalletControllerV1::class, 'dataPeriods']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletController as FinanceWalletControllerV1;

Route::prefix('wallet')->group(function () {
  Route::get('', [FinanceWalletControllerV1::class, 'get']);
  Route::get('/{id}', [FinanceWalletControllerV1::class, 'getById']);
  Route::post('', [FinanceWalletControllerV1::class, 'create']);
  Route::put('/{id}', [FinanceWalletControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceWalletControllerV1::class, 'delete']);
});

Route::get('wallet-restore/{id}', [FinanceWalletControllerV1::class, 'restore']);
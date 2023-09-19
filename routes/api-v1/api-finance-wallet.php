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

Route::get('wallet-restore/{id}', [FinanceWalletControllerV1::class, 'restore']);

# composition
Route::get('wallet-composition', [FinanceWalletControllerV1::class, 'getComposition']);
Route::put('wallet-composition', [FinanceWalletControllerV1::class, 'createComposition']);
Route::delete('wallet-composition/{id}', [FinanceWalletControllerV1::class, 'deleteComposition']);

# periods
Route::get('wallet-dataPeriods', [FinanceWalletControllerV1::class, 'dataPeriods']);

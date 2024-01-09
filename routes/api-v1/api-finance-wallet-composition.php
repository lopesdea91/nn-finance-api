<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletCompositionController as FinanceWalletCompositionControllerV1;

Route::prefix('wallet-composition')->group(function () {
  Route::get('', [FinanceWalletCompositionControllerV1::class, 'get']);
  Route::post('', [FinanceWalletCompositionControllerV1::class, 'create']);
  Route::delete('/{id}', [FinanceWalletCompositionControllerV1::class, 'delete']);
});
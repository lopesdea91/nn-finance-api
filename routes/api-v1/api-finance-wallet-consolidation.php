<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletConsolidationController as FinanceWalletConsolidationControllerV1;

Route::prefix('wallet-consolidation')->group(function () {
  /** month */
  Route::get('month', [FinanceWalletConsolidationControllerV1::class, 'processedMonth']);
  Route::post('month', [FinanceWalletConsolidationControllerV1::class, 'processMonth']);
  Route::post('month-composition', [FinanceWalletConsolidationControllerV1::class, 'createMonthComposition']);
  Route::put('month-composition', [FinanceWalletConsolidationControllerV1::class, 'updateMonthComposition']);
});

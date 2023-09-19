<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletConsolidationController as FinanceWalletConsolidationControllerV1;

Route::prefix('wallet-consolidation')->group(function () {
  /** month */
  Route::post('month', [FinanceWalletConsolidationControllerV1::class, 'processMonth']);
  Route::get('month', [FinanceWalletConsolidationControllerV1::class, 'processedMonth']);
  Route::get('month-data', [FinanceWalletConsolidationControllerV1::class, 'processedMonthData']);

  // composition
  Route::post('month-composition', [FinanceWalletConsolidationControllerV1::class, 'createMonthComposition']);
  Route::put('month-composition', [FinanceWalletConsolidationControllerV1::class, 'updateMonthComposition']);
  Route::delete('month-composition/{id}', [FinanceWalletConsolidationControllerV1::class, 'deleteMonthComposition']);

  /** year */
  Route::get('year-data', [FinanceWalletConsolidationControllerV1::class, 'processedYearData']);
});

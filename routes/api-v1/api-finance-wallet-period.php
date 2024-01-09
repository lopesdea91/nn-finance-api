<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceWalletPeriodController as FinanceWalletPeriodControllerV1;

Route::prefix('wallet-period')->group(function () {
  Route::get('', [FinanceWalletPeriodControllerV1::class, 'get']);
});
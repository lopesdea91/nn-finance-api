<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceOriginController as FinanceOriginControllerV1;

Route::prefix('origin')->group(function () {
  Route::get('', [FinanceOriginControllerV1::class, 'get']);
  Route::get('/{id}', [FinanceOriginControllerV1::class, 'getById']);
  Route::post('', [FinanceOriginControllerV1::class, 'create']);
  Route::put('/{id}', [FinanceOriginControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceOriginControllerV1::class, 'delete']);
});

Route::get('origin-restore/{id}', [FinanceOriginControllerV1::class, 'restore']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceOriginController as FinanceOriginControllerV1;

Route::prefix('origin')->group(function () {
  Route::get('/{id}/enabled', [FinanceOriginControllerV1::class, 'enabled']);
  Route::get('/{id}/disabled', [FinanceOriginControllerV1::class, 'disabled']);

  Route::get('', [FinanceOriginControllerV1::class, 'all']);
  Route::get('/{id}', [FinanceOriginControllerV1::class, 'id']);
  Route::post('', [FinanceOriginControllerV1::class, 'store']);
  Route::put('/{id}', [FinanceOriginControllerV1::class, 'update']);
  Route::delete('/{id}', [FinanceOriginControllerV1::class, 'delete']);
});

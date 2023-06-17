<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController as UserControllerV1;

Route::prefix('user')->group(function () {
  Route::get('/data', [UserControllerV1::class, 'data']);
  Route::put('/data', [UserControllerV1::class, 'updateData']);
  Route::put('/secury', [UserControllerV1::class, 'updateSecury']);
});

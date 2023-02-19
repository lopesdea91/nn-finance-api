<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\{
  AuthController as AuthControllerV1,
};

Route::prefix('auth')->group(function () {
  Route::post('sign-up', [AuthControllerV1::class, 'signUp']);
  Route::post('sign-in', [AuthControllerV1::class, 'signIn']);
  Route::post('sign-out', [AuthControllerV1::class, 'signOut']);
});

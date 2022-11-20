<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\{
    AuthController as AuthControllerV1,
    UserController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('sign-up', [AuthControllerV1::class, 'signUp']);
        Route::post('sign-in', [AuthControllerV1::class, 'signIn']);
        // Route::middleware('auth:sanctum')->post('sign-out', [AuthControllerV1::class, 'signOut']);
    });
});


Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('sign-out', [AuthControllerV1::class, 'signOut']);
    });
    Route::prefix('user')->group(function () {
        Route::get('data', [UserController::class, 'data']);
    });
});

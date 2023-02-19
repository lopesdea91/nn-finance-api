<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceController as FinanceControllerV1;

Route::get('data', [FinanceControllerV1::class, 'data']);

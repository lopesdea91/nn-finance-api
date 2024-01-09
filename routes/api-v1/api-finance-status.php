<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceStatusController as FinanceStatusControllerV1;

Route::get('status', [FinanceStatusControllerV1::class, 'get']);

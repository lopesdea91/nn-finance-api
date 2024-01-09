<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceOriginTypeController as FinanceOriginTypeControllerV1;

Route::get('origin-type', [FinanceOriginTypeControllerV1::class, 'get']);

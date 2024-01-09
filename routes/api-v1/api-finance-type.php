<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\FinanceTypeController as FinanceTypeControllerV1;

Route::get('type', [FinanceTypeControllerV1::class, 'get']);

<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\v1\{
//     FinanceWalletController as FinanceWalletControllerV1,
//     FinanceOriginController as FinanceOriginControllerV1,
//     FinanceOriginTypeController as FinanceOriginTypeControllerV1,
//     FinanceItemController as FinanceItemControllerV1,
//     FinanceTypeController as FinanceTypeControllerV1,
//     FinanceStatusController as FinanceStatusControllerV1,
// };
// use App\Models\FinanceItemModel;
// use App\Models\FinanceOriginModel;
// use App\Models\FinanceTagsModel;
// use Illuminate\Support\Facades\DB;

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

include('api-teste.php');
include('api-others.php');

Route::prefix('v1')->group(function () {
	include('api-v1/api-auth.php');

	Route::middleware(['auth:sanctum'])->group(function () {
		include('api-v1/api-user.php');

		Route::prefix('finance')->group(function () {
			include('api-v1/api-finance-origin-type.php');
			include('api-v1/api-finance-type.php');
			include('api-v1/api-finance-status.php');
			include('api-v1/api-finance-item.php');
			include('api-v1/api-finance-origin.php');
			include('api-v1/api-finance-tag.php');
			include('api-v1/api-finance-wallet.php');
			include('api-v1/api-finance-wallet-composition.php');
			include('api-v1/api-finance-wallet-consolidation.php');
			include('api-v1/api-finance-wallet-period.php');
			include('api-v1/api-finance.php');
		});
	});
});

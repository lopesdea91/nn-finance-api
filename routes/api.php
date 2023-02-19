<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\v1\{
//     FinanceWalletController as FinanceWalletControllerV1,
//     FinanceGroupController as FinanceGroupControllerV1,
//     FinanceCategoryController as FinanceCategoryControllerV1,
//     FinanceOriginController as FinanceOriginControllerV1,
//     FinanceOriginTypeController as FinanceOriginTypeControllerV1,
//     FinanceItemController as FinanceItemControllerV1,
//     FinanceTypeController as FinanceTypeControllerV1,
//     FinanceStatusController as FinanceStatusControllerV1,
// };
// use App\Models\FinanceItemModel;
// use App\Models\FinanceOriginModel;
// use App\Models\FinanceTagsModel;
// use App\Services\FinanceItemService;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

include('api-teste.php');
include('api-others.php');

Route::prefix('v1')->group(function () {
	include('api-v1/api-auth.php');

	Route::middleware('auth:sanctum')->group(function () {
		include('api-v1/api-user.php');

		Route::prefix('finance')->group(function () {
			include('api-v1/api-finance-origin-type.php');
			include('api-v1/api-finance-type.php');
			include('api-v1/api-finance-status.php');
			include('api-v1/api-finance-item.php');
			include('api-v1/api-finance-origin.php');
			include('api-v1/api-finance-tag.php');
			include('api-v1/api-finance-wallet.php');
			include('api-v1/api-finance.php');
		});
	});

	// private
	// Route::middleware('auth:sanctum')->group(function () {
	//     Route::prefix('auth')->group(function () {
	//         Route::post('sign-out', [AuthControllerV1::class, 'signOut']);
	//     });
	// Route::prefix('user')->group(function () {
	//     Route::put('/{id}', [UserController::class, 'update']);
	//     Route::get('data', [UserController::class, 'data']);
	// });
	//     Route::prefix('finance')->group(function () {

	//         Route::prefix('group')->group(function () {
	//             Route::get('', [FinanceGroupControllerV1::class, 'all']);
	//             Route::get('/{id}', [FinanceGroupControllerV1::class, 'id']);
	//             Route::post('', [FinanceGroupControllerV1::class, 'store']);
	//             Route::put('/{id}', [FinanceGroupControllerV1::class, 'update']);
	//             Route::delete('/{id}', [FinanceGroupControllerV1::class, 'delete']);
	//         });

	//         Route::prefix('category')->group(function () {
	//             Route::get('', [FinanceCategoryControllerV1::class, 'all']);
	//             Route::get('/{id}', [FinanceCategoryControllerV1::class, 'id']);
	//             Route::post('', [FinanceCategoryControllerV1::class, 'store']);
	//             Route::put('/{id}', [FinanceCategoryControllerV1::class, 'update']);
	//             Route::delete('/{id}', [FinanceCategoryControllerV1::class, 'delete']);
	//         });

	//         Route::prefix('origin')->group(function () {
	//             Route::get('', [FinanceOriginControllerV1::class, 'all']);
	//             Route::get('/{id}', [FinanceOriginControllerV1::class, 'id']);
	//             Route::post('', [FinanceOriginControllerV1::class, 'store']);
	//             Route::put('/{id}', [FinanceOriginControllerV1::class, 'update']);
	//             Route::delete('/{id}', [FinanceOriginControllerV1::class, 'delete']);
	//         });
	//         Route::get('origin-type', [FinanceOriginTypeControllerV1::class, 'all']);


	//         Route::get('type', [FinanceTypeControllerV1::class, 'all']);
	//     });
	// });
});

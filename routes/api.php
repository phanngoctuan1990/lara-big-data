<?php

use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'users', 'as' => 'user.'], function () {
    Route::get('export', [UserController::class, 'export']);
    Route::get('tracking-export/{tracking_export}', [UserController::class, 'trackingExport']);
    Route::get('download/{tracking_export}', [UserController::class, 'download'])->name('download');
});
Route::group(['prefix' => 'sales', 'as' => 'sale.'], function () {
    Route::post('import', [SaleController::class, 'import']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StripeWebhookController;

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

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->post('/logout', 'logout');
});

Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::middleware('auth:sanctum')->get('/', 'index');
    Route::middleware('auth:sanctum')->post('/', 'store');
    Route::middleware('auth:sanctum')->put('/{id}', 'update');
    Route::middleware('auth:sanctum')->get('/{id}', 'show');
});

Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
    Route::middleware('auth:sanctum')->get('/', 'index');
});

Route::controller(StoreController::class)->prefix('store')->group(function () {
    Route::get('/', 'index');
});

Route::controller(PurchaseController::class)->prefix('purchases')->group(function () {
    Route::middleware('auth:sanctum')->get('/', 'index');
    Route::middleware('auth:sanctum')->get('/{id}', 'show');
    Route::post('/', 'store');
    Route::middleware('auth:sanctum')->put('/{id}', 'update');
    Route::middleware('auth:sanctum')->get('/{id}/download', 'downloadPurchaseOrder');
});

Route::controller(StripeWebhookController::class)->prefix('stripe')->group(function () {
    Route::post('/webhook', 'handleWebhook');
});

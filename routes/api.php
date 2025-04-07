<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WishList\WishListController;
use App\Http\Controllers\OrderController;

// User order routes
Route::middleware('auth:api')->group(function () {
    Route::post('/orders/place', [OrderController::class, 'placeOrder']);
    Route::get('/orders', [OrderController::class, 'getUserOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);
    Route::put('/orders/cancel/{id}', [OrderController::class, 'cancelOrder']);
});

// Admin order routes
Route::middleware(['auth:api', 'can:viewAll,App\Models\Order'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'getAllOrders']);
    Route::put('/admin/orders/update-status/{id}', [OrderController::class, 'updateOrderStatus']);
});

Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {

    Route::post('register', [AuthController::class, 'registration']);
    Route::post('login', [AuthController::class, 'login']);

});

Route::post('/make-admin', [AuthController::class, 'makeAdmin']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::put('update_profile', [AuthController::class, 'update']);
    Route::delete('delete', [AuthController::class, 'destroy']);
});

Route::group(['prefix' => 'wishlist'], function ($router) {

    Route::post('register', [WishListController::class, 'store']);
    Route::get('show', [WishListController::class, 'index']);

});
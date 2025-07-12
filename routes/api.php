<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// Controller Admin
use App\Http\Controllers\Api\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
// Controller Customer
use App\Http\Controllers\Api\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\ReviewController;

// Route::get('images/{path}', [AdminMenuController::class, 'getImage'])->where('path', '.*');
/*
|--------------------------------------------------------------------------
| Rute Otentikasi (Publik)
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rute untuk Customer yang Sudah Login
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth:api'], function() {
    // Endpoint untuk melihat semua menu
    Route::get('menus', [CustomerMenuController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders/{order}/review', [ReviewController::class, 'store']);
});


/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => 'api'], function () {

    Route::group(['middleware' => 'auth:api_admin'], function() {
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::post('refresh', [AdminAuthController::class, 'refresh']);
        Route::post('me', [AdminAuthController::class, 'me']);

        Route::get('menus', [AdminMenuController::class, 'index']);
        Route::post('menus', [AdminMenuController::class, 'store']);
        Route::get('menus/{menu}', [AdminMenuController::class, 'show']);
        Route::post('menus/update/{menu}', [AdminMenuController::class, 'update']); 
        Route::delete('menus/{menu}', [AdminMenuController::class, 'destroy']);

        Route::get('orders', [AdminOrderController::class, 'index']);
        Route::patch('orders/{order}', [AdminOrderController::class, 'update']);
    });
});
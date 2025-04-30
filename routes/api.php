<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OutletController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\PaymentMethodController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// test route
Route::get('/test', function () {
    return response()->json(['message' => 'Test route is working!']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {


    Route::group([
        'middleware' => ['role:superadmin'],
    ], function () {
        Route::prefix('outlets')->group(function () {
            Route::get('/', [OutletController::class, 'index']);
            Route::post('/', [OutletController::class, 'store']);
            Route::put('/{outlet}', [OutletController::class, 'update']);
            Route::delete('/{outlet}', [OutletController::class, 'destroy']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::get('/{roles}', [RoleController::class, 'show']);
            Route::post('/', [RoleController::class, 'store']);
            Route::put('/{roles}', [RoleController::class, 'update']);
            Route::delete('/{roles}', [RoleController::class, 'destroy']);
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{user}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
        });

        Route::get('permissions', [RoleController::class, 'indexPermission']);

    });

    Route::get('outlets/{outlet}', [OutletController::class, 'show']);
    Route::get('user/current', [UserController::class, 'showCurrentUser']);

    Route::group([
        'prefix' => 'outlets/{outlet_id}',
        'middleware' => 'validate.outlet.access:outlet_id',
    ], function () {
        Route::prefix('products')->group(function () {
            Route::middleware('permission:view products')->group(function () {
                Route::get('/', [ProductController::class, 'index']);
                Route::get('/{product}', [ProductController::class, 'show']);
            });
            Route::middleware('permission:create products')->group(function () {
                Route::post('/', [ProductController::class, 'store']);
            });
            Route::middleware('permission:update products')->group(function () {
                Route::put('/{product}', [ProductController::class, 'update']);
            });
            Route::middleware('permission:delete products')->group(function () {
                Route::delete('/{product}', [ProductController::class, 'destroy']);
            });
        });

        Route::prefix('categories')->group(function () {
            Route::middleware('permission:view categories')->group(function () {
                Route::get('/', [CategoryController::class, 'index']);
                Route::get('/{category}', [CategoryController::class, 'show']);
            });
            Route::middleware('permission:create categories')->group(function () {
                Route::post('/', [CategoryController::class, 'store']);
            });
            Route::middleware('permission:update categories')->group(function () {
                Route::put('/{category}', [CategoryController::class, 'update']);
            });
            Route::middleware('permission:delete categories')->group(function () {
                Route::delete('/{category}', [CategoryController::class, 'destroy']);
            });
        });

        Route::prefix('vouchers')->group(function () {
            Route::middleware('permission:view vouchers')->group(function () {
                Route::get('/', [VoucherController::class, 'index']);
                Route::get('/{voucher}', [VoucherController::class, 'show']);
            });
            Route::middleware('permission:create vouchers')->group(function () {
                Route::post('/', [VoucherController::class, 'store']);
            });
            Route::middleware('permission:update vouchers')->group(function () {
                Route::put('/{voucher}', [VoucherController::class, 'update']);
            });
            Route::middleware('permission:delete vouchers')->group(function () {
                Route::delete('/{voucher}', [VoucherController::class, 'destroy']);
            });
        });

        Route::prefix('payment-methods')->group(function () {
            Route::middleware('permission:view payment methods')->group(function () {
                Route::get('/', [PaymentMethodController::class, 'index']);
                Route::get('/{payment_method}', [PaymentMethodController::class, 'show']);
            });
            Route::middleware('permission:create payment methods')->group(function () {
                Route::post('/', [PaymentMethodController::class, 'store']);
            });
            Route::middleware('permission:update payment methods')->group(function () {
                Route::put('/{payment_method}', [PaymentMethodController::class, 'update']);
            });
            Route::middleware('permission:delete payment methods')->group(function () {
                Route::delete('/{payment_method}', [PaymentMethodController::class, 'destroy']);
            });
        });
    });



    Route::post('logout', [AuthController::class, 'logout']);


});

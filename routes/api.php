<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OpenBillController;
use App\Http\Controllers\Api\OutletController;
use App\Http\Controllers\Api\PaymentMethodController as ApiPaymentMethodController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\LogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AbsensiController;


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
            Route::middleware('permission:view roles')->group(function () {
                Route::get('/', [RoleController::class, 'index']);
                Route::get('/{roles}', [RoleController::class, 'show']);
            });
            Route::middleware('permission:create roles')->group(function () {
                Route::post('/', [RoleController::class, 'store']);
            });
            Route::middleware('permission:update roles')->group(function () {
                Route::put('/{roles}', [RoleController::class, 'update']);
            });
            Route::middleware('permission:update roles')->group(function () {
                Route::delete('/{roles}', [RoleController::class, 'destroy']);
            });
        });

        Route::prefix('users')->group(function () {
            Route::middleware('permission:view users')->group(function () {
                Route::get('/', [UserController::class, 'index']);
                Route::get('/{user}', [UserController::class, 'show']);
            });
            Route::middleware('permission:create users')->group(function () {
                Route::post('/', [UserController::class, 'store']);
            });
            Route::middleware('permission:update users')->group(function () {
                Route::put('/{user}', [UserController::class, 'update']);
            });
            Route::middleware('permission:update users')->group(function () {
                Route::delete('/{user}', [UserController::class, 'destroy']);
            });
        });

        Route::get('permissions', [RoleController::class, 'indexPermission']);

        Route::middleware('permission:view activity logs')->group(function () {
            Route::get('logs', [LogController::class, 'getAllLogs']);
        });

    });

    Route::get('outlets/{outlet}', [OutletController::class, 'show']);
    Route::get('user/current', [UserController::class, 'showCurrentUser']);
    Route::get('absensi', [AbsensiController::class, 'getAllAbsensi']);


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
                Route::post('/{product}', [ProductController::class, 'update']);
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
                Route::get('/', [ApiPaymentMethodController::class, 'index']);
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

        Route::group([
            'prefix' => 'cart',
            'middleware' => ['permission:create transaction', 'permission:view transactions', 'permission:view products'],
        ], function () {
            Route::get('/', [CartController::class, 'getCart']);
            Route::post('/', [CartController::class, 'addProductToCart']);
            Route::delete('/', [CartController::class, 'clearCart']);
            Route::put('/{id_cart}', [CartController::class, 'updateCartItem']);
            Route::delete('/{id}', [CartController::class, 'removeCartItem']);
        });

        Route::prefix('open-bills')->group(function () {
            Route::middleware('permission:view open bills')->group(function () {
                Route::get('/', [OpenBillController::class, 'getOpenBills']);
                Route::get('/{bill}', [OpenBillController::class, 'getOpenBillById']);
            });
            Route::middleware('permission:create open bill')->group(function () {
                Route::post('/', [OpenBillController::class, 'createOpenBill']);
            });
            Route::middleware('permission:update open bills')->group(function () {
                Route::put('/{bill}', [OpenBillController::class, 'updateOpenBill']);
            });
            Route::middleware('permission:delete open bills')->group(function () {
                Route::delete('/{bill}', [OpenBillController::class, 'deleteOpenBill']);
            });
            Route::middleware('permission:update open bills')->group(function () {
                Route::put('/{bill}/close', [OpenBillController::class, 'closeOpenBill']);
            });
        });

        Route::prefix('transactions')->group(function () {
            Route::middleware('permission:create transaction')->group(function () {
                Route::post('/', [TransactionController::class, 'createTransaction']);
            });
            Route::middleware('permission:view transactions')->group(function () {
                Route::get('/', [TransactionController::class, 'getHistoryTransaction']);
                Route::get('/detail/{transaction}', [TransactionController::class, 'getDetailTransaction']);
                Route::group(['prefix' => 'today'], function () {
                    Route::get('/income', [TransactionController::class, 'getTodayIncome']);
                    Route::get('/sells', [TransactionController::class, 'getTodaySell']);
                    Route::get('/best-product', [TransactionController::class, 'getBestSellingProduct']);
                    Route::get('/total-transaction', [TransactionController::class, 'getSumTodayTransaction']);
                });
            });
        });

        Route::prefix('reports')->group(function () {
            Route::middleware('permission:view sales report')->group(function () {
                Route::post('/sellings', [ReportController::class, 'generateReportSellings']);
                Route::post('/sellings/export', [ReportController::class, 'exportReportSellings']);
            });

            Route::middleware('permission:view cashier report')->group(function () {
                Route::post('/cashier', [ReportController::class, 'generateReportCashier']);
                Route::post('/cashier/export', [ReportController::class, 'exportReportCashier']);
            });
        });


          // Absensi
            // "checkin attendance", "checkout attendance", "view attendance",

        Route::prefix('absensi')->group(function () {
            Route::middleware('permission:view attendance')->group(function () {
                Route::get('/{absensi}/id', [AbsensiController::class, 'getAbsensiById']);
                Route::get('/outlet', [AbsensiController::class, 'getAbsensiByOutlet']);
            });

            Route::get('/current', [AbsensiController::class, 'getAbsensiCurrentUser']);

            Route::middleware('permission:checkin attendance')->group(function () {
                Route::post('/check-in', [AbsensiController::class, 'checkInAbsensi']);
            });

            Route::middleware('permission:checkout attendance')->group(function () {
                Route::post('/check-out', [AbsensiController::class, 'checkOutAbsensi']);
            });
        });
    });



    Route::post('logout', [AuthController::class, 'logout']);


});

<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ReportController;

// Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/currencies',  [CurrencyController::class, 'index']);
    Route::get('/categories',  [CategoryController::class, 'index']);

    Route::apiResource('/wallets',      WalletController::class);
    Route::apiResource('/transactions', TransactionController::class)
        ->only(['index', 'store', 'destroy']);

    Route::get('/reports/summary-by-category/expense', [ReportController::class, 'expenseSummary']);
    Route::get('/reports/summary-by-category/income',  [ReportController::class, 'incomeSummary']);
});

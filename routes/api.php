<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\CategoryController;

use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\LendingController;
use App\Http\Controllers\Api\DashboardController;

Route::name('api.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('investments', InvestmentController::class);
    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('loans', LoanController::class);
    Route::apiResource('lendings', LendingController::class);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\Api\InvestmentAccountController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('accounts', AccountController::class);
Route::resource('transactions', TransactionController::class);
Route::resource('investments', InvestmentController::class);
Route::resource('loans', LoanController::class);
Route::resource('lendings', LendingController::class);
Route::resource('categories', CategoryController::class);

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('transfer', [TransferController::class, 'index'])->name('transfer.index');

Route::view('investment-accounts', 'investment-accounts.index')->name('investment-accounts.index');
Route::view('cash-flow', 'reports.cash-flow')->name('cash-flow');
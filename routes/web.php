<?php

use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Expense Report
Route::middleware(['auth'])->group(function () {
    Route::post('/store', [ExpenseReportController::class, 'store'])->name('expenseReport.store');
    Route::get('/form', [ExpenseReportController::class, 'index'])->name('expenseReport.form');
});

//Login
Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/checkUser', [UserController::class, 'checkLogin'])->name('user.login');
Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');


require __DIR__.'/settings.php';

<?php

use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Expense Report
Route::middleware(['auth'])->group(function () {
    Route::post('/store', [ExpenseReportController::class, 'store'])->name('expenseReport.store');
    Route::get('/form', [ExpenseReportController::class, 'index'])->name('expenseReport.form');
});

//Users
Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/checkUser', [UserController::class, 'checkLogin'])->name('user.login');
Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');
Route::post('/store', [UserController::class, 'store'])->name('users.store');
Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Uniquement pour le développement
Route::get('/test-export', [App\Http\Controllers\ExpenseReportController::class, 'export']);


require __DIR__.'/settings.php';

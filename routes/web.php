<?php

use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Expense Report
Route::middleware(['auth'])->group(function () {
    Route::post('/expense-reports', [ExpenseReportController::class, 'store'])->name('expenseReport.store');
    Route::get('/form', [ExpenseReportController::class, 'index'])->name('expenseReport.form');
});

// Users
Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('/checkUser', [UserController::class, 'checkLogin'])->name('user.login');
Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');
Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Only for development testing, to be removed in production
Route::get('/test-export', [App\Http\Controllers\ExpenseReportController::class, 'export']);
require __DIR__.'/settings.php';

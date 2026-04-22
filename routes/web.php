<?php

use App\Http\Controllers\ExpenseReportController;
use Illuminate\Support\Facades\Route;

// Expense Report
Route::get('/', [ExpenseReportController::class, 'index'])->name('home');
Route::post('/store', [ExpenseReportController::class, 'store'])->name('expenseReport.store');




Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});



require __DIR__.'/settings.php';

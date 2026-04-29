<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reports', [DailyReportController::class, 'index'])->name('reports.index');

    Route::middleware(['role:admin,operator'])->group(function () {
        Route::get('/reports/create', [DailyReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [DailyReportController::class, 'store'])->name('reports.store');

        Route::get('/reports/{report}/edit', [DailyReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{report}', [DailyReportController::class, 'update'])->name('reports.update');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/reports/{report}', [DailyReportController::class, 'destroy'])->name('reports.destroy');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    });

    Route::get('/reports/{report}', [DailyReportController::class, 'show'])->name('reports.show');
});

require __DIR__.'/auth.php';
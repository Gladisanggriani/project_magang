<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RakpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.public');
Route::get('/dashboard/silo-data', [DashboardController::class, 'siloData'])->name('dashboard.silo-data');

Route::get('/reports', [DailyReportController::class, 'index'])->name('reports.index');

Route::get('/reports/preview/monthly', [DailyReportController::class, 'previewMonthlyReport'])
    ->name('reports.preview-monthly');
// export bulanan/filter
Route::get('/reports/export/monthly', [DailyReportController::class, 'exportMonthlyExcel'])
    ->name('reports.export-monthly');

// export per laporan harian
Route::get('/reports/{report}/export-excel', [DailyReportController::class, 'exportExcel'])
    ->name('reports.export-excel');

// login
Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin,operator'])->group(function () {
        Route::get('/reports/create', [DailyReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [DailyReportController::class, 'store'])->name('reports.store');

        Route::get('/reports/{report}/edit', [DailyReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{report}', [DailyReportController::class, 'update'])->name('reports.update');

        Route::get('/rakp', [RakpController::class, 'index'])->name('rakps.index');
        Route::post('/rakp', [RakpController::class, 'store'])->name('rakps.store');
        Route::get('/rakp/export', [RakpController::class, 'export'])->name('rakps.export');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/reports/{report}', [DailyReportController::class, 'destroy'])->name('reports.destroy');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    });
});

// Detail laporan harus paling bawah agar /reports/create tidak dianggap sebagai {report}
Route::get('/reports/{report}', [DailyReportController::class, 'show'])->name('reports.show');

require __DIR__ . '/auth.php';

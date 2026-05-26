<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RakpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC / VIEWER
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.public');
Route::get('/dashboard/silo-data', [DashboardController::class, 'siloData'])->name('dashboard.silo-data');

Route::get('/reports', [DailyReportController::class, 'index'])->name('reports.index');

Route::get('/reports/preview/monthly', [DailyReportController::class, 'previewMonthlyReport'])
    ->name('reports.preview-monthly');

Route::get('/reports/export/monthly', [DailyReportController::class, 'exportMonthlyExcel'])
    ->name('reports.export-monthly');

Route::get('/reports/{report}/export-excel', [DailyReportController::class, 'exportExcel'])
    ->whereNumber('report')
    ->name('reports.export-excel');

/*
|--------------------------------------------------------------------------
| PUBLIC RKAP
|--------------------------------------------------------------------------
| Viewer boleh lihat RKAP.
*/

Route::get('/rakp', [RakpController::class, 'index'])
    ->name('rakps.index');

Route::get('/rakp/export', [RakpController::class, 'export'])
    ->name('rakps.export');

/*
|--------------------------------------------------------------------------
| LOGIN REQUIRED
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin,operator'])->group(function () {
        Route::get('/reports/create', [DailyReportController::class, 'create'])
            ->name('reports.create');

        Route::post('/reports', [DailyReportController::class, 'store'])
            ->name('reports.store');

        Route::get('/reports/{report}/edit', [DailyReportController::class, 'edit'])
            ->whereNumber('report')
            ->name('reports.edit');

        Route::put('/reports/{report}', [DailyReportController::class, 'update'])
            ->whereNumber('report')
            ->name('reports.update');

        /*
        |--------------------------------------------------------------------------
        | RKAP WRITE ACCESS
        |--------------------------------------------------------------------------
        | Hanya admin/operator yang boleh simpan/edit RKAP.
        */
        Route::post('/rakp', [RakpController::class, 'store'])
            ->name('rakps.store');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/reports/{report}', [DailyReportController::class, 'destroy'])
            ->whereNumber('report')
            ->name('reports.destroy');

        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
            ->whereNumber('user')
            ->name('users.edit');

        Route::put('/users/{user}', [UserManagementController::class, 'update'])
            ->whereNumber('user')
            ->name('users.update');
    });
});

/*
|--------------------------------------------------------------------------
| DETAIL LAPORAN
|--------------------------------------------------------------------------
*/

Route::get('/reports/{report}', [DailyReportController::class, 'show'])
    ->whereNumber('report')
    ->name('reports.show');

require __DIR__ . '/auth.php';
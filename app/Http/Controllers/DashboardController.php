<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayReport = DailyReport::with([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'bagStocks'
        ])->whereDate('report_date', $today)->first();

        if (!$todayReport) {
            $todayReport = DailyReport::with([
                'materialStocks',
                'materialReceipts',
                'materialUsages',
                'bagStocks'
            ])->latest('report_date')->first();
        }

        $startOfMonth = Carbon::now()->startOfMonth();

        $mtdProductionCm = DailyReport::whereBetween('report_date', [$startOfMonth, $today])
            ->sum('production_cm');

        $mtdProductionPacker = DailyReport::whereBetween('report_date', [$startOfMonth, $today])
            ->sum('production_packer');

        $dayOfMonth = Carbon::now()->day;

        $avgProductionCm = $dayOfMonth > 0 ? $mtdProductionCm / $dayOfMonth : 0;
        $avgProductionPacker = $dayOfMonth > 0 ? $mtdProductionPacker / $dayOfMonth : 0;

        $chartReports = DailyReport::orderBy('report_date', 'asc')
            ->whereBetween('report_date', [$startOfMonth, $today])
            ->get();

        return view('dashboard.index', compact(
            'todayReport',
            'mtdProductionCm',
            'mtdProductionPacker',
            'avgProductionCm',
            'avgProductionPacker',
            'chartReports'
        ));
    }
}
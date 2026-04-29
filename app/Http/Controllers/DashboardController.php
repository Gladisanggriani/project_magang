<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\MaterialUsage;
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

        /*
        |--------------------------------------------------------------------------
        | Ketahanan Stock
        |--------------------------------------------------------------------------
        | Rumus:
        | Ketahanan Stock = Closing Stock / Rata-rata Pemakaian Harian
        |
        | Contoh:
        | Stock Klinker = 10.000 ton
        | Total pemakaian Klinker bulan ini = 20.000 ton
        | Hari berjalan = 10 hari
        | Rata-rata pemakaian = 20.000 / 10 = 2.000 ton/hari
        | Ketahanan stock = 10.000 / 2.000 = 5 hari
        */
        $stockResistance = [];

        if ($todayReport) {
            foreach ($todayReport->materialStocks as $stock) {
                $totalUsage = MaterialUsage::where('material_name', $stock->material_name)
                    ->whereHas('dailyReport', function ($query) use ($startOfMonth, $today) {
                        $query->whereBetween('report_date', [$startOfMonth, $today]);
                    })
                    ->sum('quantity');

                $averageUsage = $dayOfMonth > 0 ? $totalUsage / $dayOfMonth : 0;

                $resistanceDays = $averageUsage > 0 ? $stock->quantity / $averageUsage : 0;

                $stockResistance[] = [
                    'material_name' => $stock->material_name,
                    'stock' => $stock->quantity,
                    'average_usage' => $averageUsage,
                    'resistance_days' => $resistanceDays,
                    'unit' => $stock->unit,
                ];
            }
        }

        return view('dashboard.index', compact(
            'todayReport',
            'mtdProductionCm',
            'mtdProductionPacker',
            'avgProductionCm',
            'avgProductionPacker',
            'chartReports',
            'stockResistance'
        ));
    }
}
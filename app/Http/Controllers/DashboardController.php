<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Rakp;
use App\Services\StockCalculationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $relations = [
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'materialIntransits',
            'bagStocks',
        ];

        $todayReport = DailyReport::with($relations)
            ->whereDate('report_date', $today)
            ->first();

        if (!$todayReport) {
            $todayReport = DailyReport::with($relations)
                ->latest('report_date')
                ->first();
        }

        $previousReport = null;

        if ($todayReport) {
            $previousReport = DailyReport::where('report_date', '<', $todayReport->report_date)
                ->orderByDesc('report_date')
                ->first();
        }

        $reportDate = $todayReport
            ? Carbon::parse($todayReport->report_date)
            : Carbon::today();

        $startOfMonth = $reportDate->copy()->startOfMonth();
        $endOfMonth = $reportDate->copy()->endOfMonth();

        $mtdProductionCm = DailyReport::whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->sum('production_cm');

        $mtdProductionPacker = DailyReport::whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->sum('production_packer');

        $mtdProductionShip = DailyReport::whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->sum('production_ship');

        $dayOfMonth = $reportDate->day;

        $avgProductionCm = $dayOfMonth > 0 ? $mtdProductionCm / $dayOfMonth : 0;
        $avgProductionPacker = $dayOfMonth > 0 ? $mtdProductionPacker / $dayOfMonth : 0;
        $avgProductionShip = $dayOfMonth > 0 ? $mtdProductionShip / $dayOfMonth : 0;

        $chartReports = DailyReport::orderBy('report_date', 'asc')
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Closing Stock
        |--------------------------------------------------------------------------
        | Rumus:
        | Closing Stock = Silo Semen + Produksi Semen - Produksi Packer
        */
        $closingStock = 0;
        $totalTruck = 0;

        if ($todayReport) {
            $closingStock = StockCalculationService::closingStock(
                $todayReport->silo_semen,
                $todayReport->production_cm,
                $todayReport->production_packer
            );

            $totalTruck =
                ($todayReport->truck_packer_area ?? 0)
                + ($todayReport->truck_emplacement_area ?? 0);
        }

        /*
        |--------------------------------------------------------------------------
        | RKAP dan Ketahanan Stock
        |--------------------------------------------------------------------------
        | Rumus sementara ada di app/Services/StockCalculationService.php
        */
        $currentYear = $reportDate->year;
        $currentMonth = $reportDate->month;
        $daysInMonth = $reportDate->daysInMonth;

        $currentRakp = Rakp::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->where('material_name', 'Semen')
            ->value('value');

        $stockResistanceDays = StockCalculationService::stockResistance(
            $closingStock,
            $currentRakp,
            $daysInMonth
        );

        /*
        |--------------------------------------------------------------------------
        | Ketahanan Stock Material Lama
        |--------------------------------------------------------------------------
        | Tetap dikirim agar popup/list dashboard lama tidak error.
        */
        $stockResistance = [];

        if ($todayReport) {
            foreach ($todayReport->materialStocks as $stock) {
                $todayUsage = $todayReport->materialUsages
                    ->where('material_name', $stock->material_name)
                    ->sum('quantity');

                $resistanceDays = $todayUsage > 0
                    ? $stock->quantity / $todayUsage
                    : 0;

                $stockResistance[] = [
                    'material_name' => $stock->material_name,
                    'stock' => $stock->quantity,
                    'today_usage' => $todayUsage,
                    'resistance_days' => $resistanceDays,
                    'unit' => $stock->unit,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Silo Premium
        |--------------------------------------------------------------------------
        */
        $silos = $this->buildSilos($todayReport, $previousReport);

        /*
        |--------------------------------------------------------------------------
        | Variabel lama tetap dikirim agar blade lama tidak error
        |--------------------------------------------------------------------------
        */
        $mainSilo = $silos[0] ?? null;

        $siloCapacity = $mainSilo['capacity'] ?? 1000;
        $siloValue = $mainSilo['value'] ?? 0;
        $siloPercentage = $mainSilo['percentage'] ?? 0;
        $previousSiloValue = $mainSilo['previous_value'] ?? 0;
        $siloTrend = $mainSilo['trend'] ?? 'stable';

        return view('dashboard.index', compact(
            'todayReport',
            'mtdProductionCm',
            'mtdProductionPacker',
            'mtdProductionShip',
            'avgProductionCm',
            'avgProductionPacker',
            'avgProductionShip',
            'chartReports',
            'closingStock',
            'totalTruck',
            'currentRakp',
            'currentYear',
            'currentMonth',
            'daysInMonth',
            'stockResistanceDays',
            'stockResistance',
            'silos',
            'siloCapacity',
            'siloValue',
            'siloPercentage',
            'previousSiloValue',
            'siloTrend'
        ));
    }

    public function siloData(): JsonResponse
    {
        $today = Carbon::today();

        $todayReport = DailyReport::whereDate('report_date', $today)->first();

        if (!$todayReport) {
            $todayReport = DailyReport::latest('report_date')->first();
        }

        $previousReport = null;

        if ($todayReport) {
            $previousReport = DailyReport::where('report_date', '<', $todayReport->report_date)
                ->orderByDesc('report_date')
                ->first();
        }

        return response()->json([
            'silos' => $this->buildSilos($todayReport, $previousReport),
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    private function resolveSiloLevel(float $percentage): array
    {
        if ($percentage <= 30) {
            return [
                'text' => 'Rendah',
                'class' => 'level-low',
            ];
        }

        if ($percentage <= 70) {
            return [
                'text' => 'Sedang',
                'class' => 'level-medium',
            ];
        }

        return [
            'text' => 'Tinggi',
            'class' => 'level-high',
        ];
    }

    private function buildSilos(?DailyReport $todayReport, ?DailyReport $previousReport): array
    {
        $configs = [
            [
                'code' => 'silo_semen',
                'label' => 'Silo Semen',
                'field' => 'silo_semen',
                'capacity' => 11000,
                'unit' => 'Ton',
            ],
        ];

        return collect($configs)->map(function ($config) use ($todayReport, $previousReport) {
            $value = $todayReport ? (float) ($todayReport->{$config['field']} ?? 0) : 0;
            $previousValue = $previousReport ? (float) ($previousReport->{$config['field']} ?? 0) : $value;

            $percentage = $config['capacity'] > 0 ? ($value / $config['capacity']) * 100 : 0;
            $percentage = max(0, min(100, $percentage));

            $trend = 'stable';

            if ($value > $previousValue) {
                $trend = 'up';
            } elseif ($value < $previousValue) {
                $trend = 'down';
            }

            $level = $this->resolveSiloLevel($percentage);

            return [
                'code' => $config['code'],
                'label' => $config['label'],
                'field' => $config['field'],
                'value' => $value,
                'previous_value' => $previousValue,
                'capacity' => (float) $config['capacity'],
                'unit' => $config['unit'],
                'percentage' => round($percentage, 1),
                'trend' => $trend,
                'delta' => round($value - $previousValue, 2),
                'level_text' => $level['text'],
                'level_class' => $level['class'],
                'formatted_value' => number_format($value, 2, ',', '.') . ' ' . $config['unit'],
                'formatted_capacity' => number_format((float) $config['capacity'], 2, ',', '.') . ' ' . $config['unit'],
                'formatted_percentage' => number_format($percentage, 1, ',', '.') . '%',
                'formatted_delta' => number_format(abs($value - $previousValue), 2, ',', '.') . ' ' . $config['unit'],
            ];
        })->values()->all();
    }
}

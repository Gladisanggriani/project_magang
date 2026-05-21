<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\MaterialUsage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayReport = DailyReport::with([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'materialIntransits',
            'bagStocks'
        ])->whereDate('report_date', $today)->first();

        if (!$todayReport) {
            $todayReport = DailyReport::with([
                'materialStocks',
                'materialReceipts',
                'materialUsages',
                'materialIntransits',
                'bagStocks'
            ])->latest('report_date')->first();
        }

        $previousReport = null;

        if ($todayReport) {
            $previousReport = DailyReport::where('report_date', '<', $todayReport->report_date)
                ->orderByDesc('report_date')
                ->first();
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
            'avgProductionCm',
            'avgProductionPacker',
            'chartReports',
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
        /*
        |--------------------------------------------------------------------------
        | Konfigurasi Silo
        |--------------------------------------------------------------------------
        | Saat ini database kamu baru punya field silo_semen.
        | Kalau nanti ada Silo 1, Silo 2, Silo 3, tinggal tambah config di sini.
        |
        | Capacity 1000 ini sementara. Nanti ganti sesuai kapasitas asli silo.
        */
        $configs = [
            [
                'code' => 'silo_semen',
                'label' => 'Silo Semen',
                'field' => 'silo_semen',
                'capacity' => 900000,
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
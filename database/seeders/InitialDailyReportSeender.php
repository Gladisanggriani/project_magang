<?php

namespace Database\Seeders;

use App\Models\BagStock;
use App\Models\DailyReport;
use App\Models\MaterialIntransit;
use App\Models\MaterialReceipt;
use App\Models\MaterialUsage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InitialDailyReportSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()
            ->whereIn('role', ['admin', 'operator'])
            ->value('id') ?? User::query()->value('id');

        if (!$userId) {
            throw new RuntimeException('Seeder gagal: belum ada user admin/operator di database.');
        }

        $reports = [
            [
                'report_date' => '2026-05-01',

                'cement_mill_status' => 'RUN',
                'cement_mill_note' => 'Operasional lancar',

                'feed' => 2520.78,
                'blaine' => 3400,
                'sieving' => 2.5,
                'production_cm' => 1200,
                'production_ship' => 0,
                'running_hours' => 18.5,
                'clinker_factor' => 70.5,
                'silo_semen' => 5000,

                'packer1_status' => 'READY',
                'packer1_note' => 'Normal',
                'packer2_status' => 'READY',
                'packer2_note' => 'Normal',

                'truck_packer_area' => 10,
                'truck_emplacement_area' => 5,
                'production_packer' => 900,

                /*
                |--------------------------------------------------------------------------
                | Stock awal
                |--------------------------------------------------------------------------
                | Dipakai untuk tanggal pertama jika belum ada laporan sebelumnya.
                */
                'initial_stocks' => [
                    'Semen' => 5000,
                    'Klinker' => 3000,
                    'Limestone' => 800,
                    'Gypsum' => 500,
                    'Pozzolan' => 400,
                    'Solar' => 1000,
                    'Gas' => 700,
                ],

                'receipts' => [
                    'Semen' => 1000,
                    'Klinker' => 500,
                    'Limestone' => 100,
                    'Gypsum' => 50,
                    'Pozzolan' => 75,
                ],

                'intransits' => [
                    'Semen' => 200,
                    'Klinker' => 150,
                    'Limestone' => 50,
                    'Gypsum' => 25,
                    'Pozzolan' => 30,
                ],

                'usages' => [
                    'Klinker' => 400,
                    'Gypsum Natural' => 20,
                    'Gypsum Purified' => 10,
                    'Pozzolan' => 30,
                    'Wet Fly Ash' => 15,
                    'Dry Fly Ash' => 25,
                    'Limestone' => 60,
                    'Solar' => 100,
                    'Gas' => 80,
                ],

                'bags' => [
                    'BB 50 KG SP' => 10000,
                    'BB 40 KG SP' => 8000,
                    'Dinamik 50 KG' => 5000,
                    'Dinamik 40 KG' => 3000,
                    'Merdeka 50 KG' => 4000,
                    'Merdeka 40 KG' => 2000,
                ],
            ],

            [
                'report_date' => '2026-05-02',

                'cement_mill_status' => 'RUN',
                'cement_mill_note' => 'Operasional normal',

                'feed' => 2600,
                'blaine' => 3450,
                'sieving' => 2.3,
                'production_cm' => 1300,
                'production_ship' => 0,
                'running_hours' => 19,
                'clinker_factor' => 71,
                'silo_semen' => 5200,

                'packer1_status' => 'READY',
                'packer1_note' => 'Normal',
                'packer2_status' => 'READY',
                'packer2_note' => 'Normal',

                'truck_packer_area' => 8,
                'truck_emplacement_area' => 4,
                'production_packer' => 950,

                'receipts' => [
                    'Semen' => 800,
                    'Klinker' => 600,
                    'Limestone' => 120,
                    'Gypsum' => 60,
                    'Pozzolan' => 90,
                ],

                'intransits' => [
                    'Semen' => 100,
                    'Klinker' => 200,
                    'Limestone' => 40,
                    'Gypsum' => 20,
                    'Pozzolan' => 35,
                ],

                'usages' => [
                    'Klinker' => 420,
                    'Gypsum Natural' => 25,
                    'Gypsum Purified' => 12,
                    'Pozzolan' => 35,
                    'Wet Fly Ash' => 18,
                    'Dry Fly Ash' => 28,
                    'Limestone' => 70,
                    'Solar' => 110,
                    'Gas' => 85,
                ],

                'bags' => [
                    'BB 50 KG SP' => 9500,
                    'BB 40 KG SP' => 7600,
                    'Dinamik 50 KG' => 4800,
                    'Dinamik 40 KG' => 2900,
                    'Merdeka 50 KG' => 3800,
                    'Merdeka 40 KG' => 1900,
                ],
            ],
        ];

        DB::transaction(function () use ($reports, $userId) {
            foreach ($reports as $data) {
                $report = DailyReport::updateOrCreate(
                    [
                        'report_date' => $data['report_date'],
                    ],
                    [
                        'cement_mill_status' => $data['cement_mill_status'],
                        'cement_mill_note' => $data['cement_mill_note'],

                        'feed' => $data['feed'] ?? 0,
                        'blaine' => $data['blaine'] ?? 0,
                        'sieving' => $data['sieving'] ?? 0,
                        'production_cm' => $data['production_cm'] ?? 0,
                        'production_ship' => $data['production_ship'] ?? 0,
                        'running_hours' => $data['running_hours'] ?? 0,
                        'clinker_factor' => $data['clinker_factor'] ?? 0,
                        'silo_semen' => $data['silo_semen'] ?? 0,

                        'packer1_status' => $data['packer1_status'],
                        'packer1_note' => $data['packer1_note'],
                        'packer2_status' => $data['packer2_status'],
                        'packer2_note' => $data['packer2_note'],

                        'truck_packer_area' => $data['truck_packer_area'] ?? 0,
                        'truck_emplacement_area' => $data['truck_emplacement_area'] ?? 0,
                        'production_packer' => $data['production_packer'] ?? 0,

                        'created_by' => $userId,
                    ]
                );

                $this->saveDetailData($report, $data);
                $this->calculateMaterialStocks($report, $data['initial_stocks'] ?? []);
            }
        });
    }

    private function saveDetailData(DailyReport $report, array $data): void
    {
        $report->materialStocks()->delete();
        $report->materialReceipts()->delete();
        $report->materialUsages()->delete();
        $report->materialIntransits()->delete();
        $report->bagStocks()->delete();

        foreach ($data['receipts'] ?? [] as $materialName => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            MaterialReceipt::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $quantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($data['usages'] ?? [] as $materialName => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            MaterialUsage::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $quantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($data['intransits'] ?? [] as $materialName => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            MaterialIntransit::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $quantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($data['bags'] ?? [] as $bagType => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            BagStock::create([
                'daily_report_id' => $report->id,
                'bag_type' => $bagType,
                'quantity' => $quantity,
                'unit' => 'lembar',
            ]);
        }
    }

    private function calculateMaterialStocks(DailyReport $report, array $initialStocks = []): void
    {
        $report->load([
            'materialReceipts',
            'materialUsages',
            'materialStocks',
        ]);

        $previousReport = DailyReport::where('report_date', '<', $report->report_date)
            ->orderByDesc('report_date')
            ->with('materialStocks')
            ->first();

        $materialNames = collect()
            ->merge(array_keys($initialStocks))
            ->merge($report->materialReceipts->pluck('material_name'))
            ->merge($report->materialUsages->pluck('material_name'));

        if ($previousReport) {
            $materialNames = $materialNames->merge(
                $previousReport->materialStocks->pluck('material_name')
            );
        }

        $materialNames = $materialNames
            ->filter()
            ->unique()
            ->values();

        foreach ($materialNames as $materialName) {
            $previousStock = 0;

            if ($previousReport) {
                $previousMaterialStock = $previousReport->materialStocks
                    ->firstWhere('material_name', $materialName);

                $previousStock = $previousMaterialStock
                    ? (float) $previousMaterialStock->quantity
                    : 0;
            } else {
                $previousStock = (float) ($initialStocks[$materialName] ?? 0);
            }

            $receiptTotal = (float) $report->materialReceipts
                ->where('material_name', $materialName)
                ->sum('quantity');

            $usageTotal = (float) $report->materialUsages
                ->where('material_name', $materialName)
                ->sum('quantity');

            $stockQuantity = $previousStock + $receiptTotal - $usageTotal;

            $report->materialStocks()->updateOrCreate(
                [
                    'material_name' => $materialName,
                ],
                [
                    'quantity' => $stockQuantity,
                    'unit' => $this->resolveMaterialUnit($materialName),
                ]
            );
        }
    }

    private function resolveMaterialUnit(string $materialName): string
    {
        return in_array($materialName, ['Solar', 'Gas']) ? 'liter' : 'ton';
    }
}
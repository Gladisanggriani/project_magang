<?php

namespace App\Http\Controllers;

use App\Models\BagStock;
use App\Models\DailyReport;
use App\Models\MaterialIntransit;
use App\Models\MaterialReceipt;
use App\Models\MaterialUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyReport::query()
            ->orderByDesc('report_date');

        if ($request->filled('date')) {
            $query->whereDate('report_date', $request->date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('report_date', Carbon::parse($request->month)->month)
                ->whereYear('report_date', Carbon::parse($request->month)->year);
        }

        if ($request->filled('day')) {
            $query->whereRaw('DAYNAME(report_date) = ?', [$request->day]);
        }

        if ($request->filled('status')) {
            $query->where(function ($q) use ($request) {
                $q->where('cement_mill_status', $request->status)
                    ->orWhere('packer1_status', $request->status)
                    ->orWhere('packer2_status', $request->status);
            });
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $this->validateReport($request);

        DB::transaction(function () use ($request) {
            $report = DailyReport::create([
                'report_date' => $request->report_date,

                'cement_mill_status' => $request->cement_mill_status,
                'cement_mill_note' => $request->cement_mill_note,

                'feed' => $this->normalizeNumber($request->feed),
                'blaine' => $this->normalizeNumber($request->blaine),
                'sieving' => $this->normalizeNumber($request->sieving),
                'production_cm' => $this->normalizeNumber($request->production_cm),
                'production_ship' => $this->normalizeNumber($request->production_ship),
                'running_hours' => $this->normalizeNumber($request->running_hours),
                'clinker_factor' => $this->normalizeNumber($request->clinker_factor),
                'silo_semen' => $this->normalizeNumber($request->silo_semen),

                'packer1_status' => $request->packer1_status,
                'packer1_note' => $request->packer1_note,
                'packer2_status' => $request->packer2_status,
                'packer2_note' => $request->packer2_note,

                'truck_packer_area' => (int) $this->normalizeNumber($request->truck_packer_area),
                'truck_emplacement_area' => (int) $this->normalizeNumber($request->truck_emplacement_area),
                'production_packer' => $this->normalizeNumber($request->production_packer),

                'created_by' => Auth::id(),
            ]);

            $this->saveDetailData($report, $request);

            $report->load([
                'materialReceipts',
                'materialUsages',
                'materialStocks',
            ]);

            $this->calculateMaterialStocks($report);
        });

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan harian berhasil disimpan.');
    }

    public function show(DailyReport $report)
    {
        $report->load([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'materialIntransits',
            'bagStocks',
        ]);

        $totalTruck =
            ($report->truck_packer_area ?? 0)
            + ($report->truck_emplacement_area ?? 0);

        $closingStock =
            ($report->silo_semen ?? 0)
            + ($report->production_cm ?? 0)
            - ($report->production_packer ?? 0);

        return view('reports.show', compact(
            'report',
            'totalTruck',
            'closingStock'
        ));
    }

    public function edit(DailyReport $report)
    {
        $report->load([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'materialIntransits',
            'bagStocks',
        ]);

        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, DailyReport $report)
    {
        $this->validateReport($request, $report);

        DB::transaction(function () use ($request, $report) {
            $report->update([
                'report_date' => $request->report_date,

                'cement_mill_status' => $request->cement_mill_status,
                'cement_mill_note' => $request->cement_mill_note,

                'feed' => $this->normalizeNumber($request->feed),
                'blaine' => $this->normalizeNumber($request->blaine),
                'sieving' => $this->normalizeNumber($request->sieving),
                'production_cm' => $this->normalizeNumber($request->production_cm),
                'production_ship' => $this->normalizeNumber($request->production_ship),
                'running_hours' => $this->normalizeNumber($request->running_hours),
                'clinker_factor' => $this->normalizeNumber($request->clinker_factor),
                'silo_semen' => $this->normalizeNumber($request->silo_semen),

                'packer1_status' => $request->packer1_status,
                'packer1_note' => $request->packer1_note,
                'packer2_status' => $request->packer2_status,
                'packer2_note' => $request->packer2_note,

                'truck_packer_area' => (int) $this->normalizeNumber($request->truck_packer_area),
                'truck_emplacement_area' => (int) $this->normalizeNumber($request->truck_emplacement_area),
                'production_packer' => $this->normalizeNumber($request->production_packer),
            ]);

            $this->saveDetailData($report, $request);

            $report->load([
                'materialReceipts',
                'materialUsages',
                'materialStocks',
            ]);

            $this->calculateMaterialStocks($report);
        });

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan harian berhasil diperbarui.');
    }

    public function destroy(DailyReport $report)
    {
        DB::transaction(function () use ($report) {
            $report->materialStocks()->delete();
            $report->materialReceipts()->delete();
            $report->materialUsages()->delete();
            $report->materialIntransits()->delete();
            $report->bagStocks()->delete();

            $report->delete();
        });

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan harian berhasil dihapus.');
    }

    private function validateReport(Request $request, ?DailyReport $report = null): void
    {
        $request->validate([
            'report_date' => ['required', 'date'],

            'cement_mill_status' => ['nullable', 'string'],
            'cement_mill_note' => ['nullable', 'string'],

            'feed' => ['nullable', 'string'],
            'blaine' => ['nullable', 'string'],
            'sieving' => ['nullable', 'string'],
            'production_cm' => ['nullable', 'string'],
            'production_ship' => ['nullable', 'string'],
            'running_hours' => ['nullable', 'string'],
            'clinker_factor' => ['nullable', 'string'],
            'silo_semen' => ['nullable', 'string'],

            'packer1_status' => ['nullable', 'string'],
            'packer1_note' => ['nullable', 'string'],
            'packer2_status' => ['nullable', 'string'],
            'packer2_note' => ['nullable', 'string'],

            'truck_packer_area' => ['nullable', 'string'],
            'truck_emplacement_area' => ['nullable', 'string'],
            'production_packer' => ['nullable', 'string'],

            'receipts' => ['nullable', 'array'],
            'receipts.*' => ['nullable', 'string'],

            'usages' => ['nullable', 'array'],
            'usages.*' => ['nullable', 'string'],

            'intransits' => ['nullable', 'array'],
            'intransits.*' => ['nullable', 'string'],

            'bags' => ['nullable', 'array'],
            'bags.*' => ['nullable', 'string'],
        ]);
    }

    private function saveDetailData(DailyReport $report, Request $request): void
    {
        /*
        |--------------------------------------------------------------------------
        | Stock material tidak disimpan dari input manual.
        |--------------------------------------------------------------------------
        | Stock material akan dihitung otomatis oleh calculateMaterialStocks().
        */
        $report->materialStocks()->delete();
        $report->materialReceipts()->delete();
        $report->materialUsages()->delete();
        $report->materialIntransits()->delete();
        $report->bagStocks()->delete();

        foreach ($request->receipts ?? [] as $materialName => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            MaterialReceipt::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($request->usages ?? [] as $materialName => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            MaterialUsage::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($request->intransits ?? [] as $materialName => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            MaterialIntransit::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($request->bags ?? [] as $bagType => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            BagStock::create([
                'daily_report_id' => $report->id,
                'bag_type' => $bagType,
                'quantity' => $normalizedQuantity,
                'unit' => 'lembar',
            ]);
        }
    }

    private function calculateMaterialStocks(DailyReport $report): void
    {
        $report->loadMissing([
            'materialReceipts',
            'materialUsages',
            'materialStocks',
        ]);

        $previousReport = DailyReport::where('report_date', '<', $report->report_date)
            ->orderByDesc('report_date')
            ->with('materialStocks')
            ->first();

        $materialNames = collect()
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

            $previousMaterialStock = $previousReport
                ? $previousReport->materialStocks->firstWhere('material_name', $materialName)
                : null;

            if ($previousMaterialStock) {
                $previousStock = (float) ($previousMaterialStock->quantity ?? 0);
            }

            $receiptTotal = (float) $report->materialReceipts
                ->where('material_name', $materialName)
                ->sum('quantity');

            $usageTotal = (float) $report->materialUsages
                ->where('material_name', $materialName)
                ->sum('quantity');

            /*
            |--------------------------------------------------------------------------
            | Rumus Stock Material Otomatis
            |--------------------------------------------------------------------------
            | Stock hari ini = Stock hari sebelumnya + Penerimaan hari ini - Pemakaian hari ini
            */
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
        $literMaterials = [
            'Solar',
            'Gas',
        ];

        return in_array($materialName, $literMaterials) ? 'liter' : 'ton';
    }

    private function isEmptyDetailValue($rawValue, float $normalizedValue): bool
    {
        return ($rawValue === null || $rawValue === '') && $normalizedValue <= 0;
    }

    private function normalizeNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim((string) $value);

        /*
        |--------------------------------------------------------------------------
        | Format angka Indonesia
        |--------------------------------------------------------------------------
        | 56.000,50 menjadi 56000.50
        */
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0;
    }
}
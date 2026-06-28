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
        $reports = $this->filteredReportsQuery($request)
            ->orderByDesc('report_date')
            ->paginate(10)
            ->withQueryString();

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

            $start = strtotime($request->start_time);
            $end = strtotime($request->end_time);

            $runningHours = 0;

            if ($start && $end) {

                if ($end < $start) {
                    $end += 86400;
                }

                $runningHours = round(($end - $start) / 3600, 2);
            }

            $productionCM = $this->normalizeNumber($request->production_cm);

            // PERBAIKAN: Menangkap nilai Packer 1 dan Packer 2
            $productionPacker1 = $this->normalizeNumber($request->production_packer1);
            $productionPacker2 = $this->normalizeNumber($request->production_packer2);
            $productionPacker = $this->normalizeNumber($request->production_packer);

            $stockAwalSilo = $this->normalizeNumber($request->stock_awal_silo);

            $feed = 0;

            if ($runningHours > 0) {
                $feed = round($productionCM / $runningHours, 2);
            }

            $siloSemen = $stockAwalSilo + $productionCM - $productionPacker;

            $report = DailyReport::create([
                'report_date' => $request->report_date,

                'cement_mill_status' => $request->cement_mill_status,
                'cement_mill_note' => $request->cement_mill_note,

                'feed' => $feed,
                'blaine' => $this->normalizeNumber($request->blaine),
                'sieving' => $this->normalizeNumber($request->sieving),

                'production_cm' => $productionCM,
                'production_ship' => $this->normalizeNumber($request->production_ship),

                'running_hours' => $runningHours,

                'clinker_factor' => $this->normalizeNumber($request->clinker_factor),

                'start_time' => $request->start_time,
                'end_time' => $request->end_time,

                'stock_awal_silo' => $stockAwalSilo,
                'silo_semen' => $siloSemen,

                'packer1_status' => $request->packer1_status,
                'packer1_note' => $request->packer1_note,

                'packer2_status' => $request->packer2_status,
                'packer2_note' => $request->packer2_note,

                'truck_packer_area' => (int) $this->normalizeNumber($request->truck_packer_area),
                'truck_emplacement_area' => (int) $this->normalizeNumber($request->truck_emplacement_area),

                // PERBAIKAN: Menyimpan nilai Packer 1 dan Packer 2 ke database
                'production_packer1' => $productionPacker1,
                'production_packer2' => $productionPacker2,
                'production_packer' => $productionPacker,

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

            $start = strtotime($request->start_time);
            $end = strtotime($request->end_time);

            $runningHours = 0;

            if ($start && $end) {

                if ($end < $start) {
                    $end += 86400;
                }

                $runningHours = round(($end - $start) / 3600, 2);
            }

            $productionCM = $this->normalizeNumber($request->production_cm);

            // PERBAIKAN: Menangkap nilai Packer 1 dan Packer 2 saat update
            $productionPacker1 = $this->normalizeNumber($request->production_packer1);
            $productionPacker2 = $this->normalizeNumber($request->production_packer2);
            $productionPacker = $this->normalizeNumber($request->production_packer);

            $stockAwalSilo = $this->normalizeNumber($request->stock_awal_silo);

            $feed = 0;

            if ($runningHours > 0) {
                $feed = round($productionCM / $runningHours, 2);
            }

            $siloSemen = $stockAwalSilo + $productionCM - $productionPacker;

            $report->update([
                'report_date' => $request->report_date,

                'cement_mill_status' => $request->cement_mill_status,
                'cement_mill_note' => $request->cement_mill_note,

                'feed' => $feed,
                'blaine' => $this->normalizeNumber($request->blaine),
                'sieving' => $this->normalizeNumber($request->sieving),

                'production_cm' => $productionCM,
                'production_ship' => $this->normalizeNumber($request->production_ship),

                'running_hours' => $runningHours,

                'clinker_factor' => $this->normalizeNumber($request->clinker_factor),

                'start_time' => $request->start_time,
                'end_time' => $request->end_time,

                'stock_awal_silo' => $stockAwalSilo,
                'silo_semen' => $siloSemen,

                'packer1_status' => $request->packer1_status,
                'packer1_note' => $request->packer1_note,

                'packer2_status' => $request->packer2_status,
                'packer2_note' => $request->packer2_note,

                'truck_packer_area' => (int) $this->normalizeNumber($request->truck_packer_area),
                'truck_emplacement_area' => (int) $this->normalizeNumber($request->truck_emplacement_area),

                // PERBAIKAN: Update nilai Packer 1 dan Packer 2 ke database
                'production_packer1' => $productionPacker1,
                'production_packer2' => $productionPacker2,
                'production_packer' => $productionPacker,
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

    public function previewMonthlyReport(Request $request)
    {
        $reports = $this->filteredReportsQuery($request)
            ->with([
                'materialStocks',
                'materialReceipts',
                'materialUsages',
                'materialIntransits',
                'bagStocks',
            ])
            ->orderBy('report_date')
            ->get();

        $filterTitle = $this->getFilterTitle($request);
        $filterLabel = $filterTitle;

        return view('reports.preview-monthly', compact(
            'reports',
            'filterTitle',
            'filterLabel'
        ));
    }

    public function exportMonthlyExcel(Request $request)
    {
        $reports = $this->filteredReportsQuery($request)
            ->with([
                'materialStocks',
                'materialReceipts',
                'materialUsages',
                'materialIntransits',
                'bagStocks',
            ])
            ->orderBy('report_date')
            ->get();

        $filterTitle = $this->getFilterTitle($request);
        $filterLabel = $filterTitle;

        $filename = 'rekap-laporan-' . now()->format('Ymd-His') . '.xls';

        $html = view('exports.monthly-report-excel', compact(
            'reports',
            'filterTitle',
            'filterLabel'
        ))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

    public function exportExcel(DailyReport $report)
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

        $date = Carbon::parse($report->report_date)->format('Y-m-d');
        $filename = 'laporan-harian-' . $date . '.xls';

        $html = view('exports.daily-report-excel', compact(
            'report',
            'totalTruck',
            'closingStock'
        ))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
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

    private function filteredReportsQuery(Request $request)
    {
        $query = DailyReport::query();

        if ($request->filled('report_date')) {
            $query->whereDate('report_date', $request->report_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('report_date', (int) $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('report_date', (int) $request->year);
        }

        if ($request->filled('weekday')) {
            $query->whereRaw('WEEKDAY(report_date) + 1 = ?', [(int) $request->weekday]);
        }

        if ($request->filled('status')) {
            $query->where(function ($q) use ($request) {
                $q->where('cement_mill_status', $request->status)
                    ->orWhere('packer1_status', $request->status)
                    ->orWhere('packer2_status', $request->status);
            });
        }

        return $query;
    }

    private function getFilterTitle(Request $request): string
    {
        $monthNames = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $weekdayNames = [
            '1' => 'Senin',
            '2' => 'Selasa',
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jumat',
            '6' => 'Sabtu',
            '7' => 'Minggu',
        ];

        $parts = [];

        if ($request->filled('report_date')) {
            $parts[] = 'Tanggal ' . Carbon::parse($request->report_date)->format('d-m-Y');
        }

        if ($request->filled('month')) {
            $monthKey = str_pad((string) $request->month, 2, '0', STR_PAD_LEFT);
            $parts[] = 'Bulan ' . ($monthNames[$monthKey] ?? $request->month);
        }

        if ($request->filled('year')) {
            $parts[] = 'Tahun ' . $request->year;
        }

        if ($request->filled('weekday')) {
            $parts[] = 'Hari ' . ($weekdayNames[(string) $request->weekday] ?? '-');
        }

        if ($request->filled('status')) {
            $parts[] = 'Status ' . $request->status;
        }

        return count($parts) > 0 ? implode(' - ', $parts) : 'Semua Laporan';
    }

    private function saveDetailData(DailyReport $report, Request $request): void
    {
        $report->materialStocks()->delete();
        $report->materialReceipts()->delete();
        $report->materialUsages()->delete();
        $report->materialIntransits()->delete();
        $report->bagStocks()->delete();

        foreach ($request->receipts ?? [] as $materialKey => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            $materialName = str_replace('_', ' ', $materialKey);

            MaterialReceipt::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($request->usages ?? [] as $materialKey => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            $materialName = str_replace('_', ' ', $materialKey);

            MaterialUsage::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($request->intransits ?? [] as $materialKey => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            $materialName = str_replace('_', ' ', $materialKey);

            MaterialIntransit::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $this->resolveMaterialUnit($materialName),
            ]);
        }

        foreach ($request->bags ?? [] as $bagKey => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            $bagType = str_replace('_', ' ', $bagKey);

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

    private function validateReport(Request $request, ?DailyReport $report = null): array
    {
        $reportId = $report ? $report->id : null;

        return $request->validate([
            'report_date' => 'required|date|unique:daily_reports,report_date,' . $reportId,

            'cement_mill_status' => 'required|in:RUN,STOP,MAINTENANCE,TROUBLE',
            'packer1_status' => 'required|in:READY,MAINTENANCE,STOP,TROUBLE',
            'packer2_status' => 'required|in:READY,MAINTENANCE,STOP,TROUBLE',

            'cement_mill_note' => 'nullable|string|max:255',
            'packer1_note' => 'nullable|string|max:255',
            'packer2_note' => 'nullable|string|max:255',

            'blaine' => 'nullable|string',
            'sieving' => 'nullable|string',
            'production_cm' => 'nullable|string',
            'production_ship' => 'nullable|string',
            'clinker_factor' => 'nullable|string',
            'start_time' => 'required',
            'end_time' => 'required',

            'stock_awal_silo' => 'nullable|string',

            'feed' => 'nullable',
            'running_hours' => 'nullable',
            'silo_semen' => 'nullable',

            'truck_packer_area' => 'nullable|string',
            'truck_emplacement_area' => 'nullable|string',

            // PERBAIKAN: Validasi input Packer 1 dan 2
            'production_packer1' => 'nullable|string',
            'production_packer2' => 'nullable|string',
            'production_packer' => 'nullable|string',

            'receipts' => 'nullable|array',
            'receipts.*' => 'nullable|string',

            'usages' => 'nullable|array',
            'usages.*' => 'nullable|string',

            'intransits' => 'nullable|array',
            'intransits.*' => 'nullable|string',

            'bags' => 'nullable|array',
            'bags.*' => 'nullable|string',
        ], [
            'report_date.required' => 'Tanggal laporan wajib diisi.',
            'report_date.date' => 'Format tanggal laporan tidak valid.',
            'report_date.unique' => 'Laporan untuk tanggal ini sudah ada.',

            'cement_mill_status.required' => 'Operational Cement Mill wajib dipilih.',
            'cement_mill_status.in' => 'Operational Cement Mill tidak valid.',

            'packer1_status.required' => 'Kondisi Packer 1 wajib dipilih.',
            'packer1_status.in' => 'Kondisi Packer 1 tidak valid.',

            'packer2_status.required' => 'Kondisi Packer 2 wajib dipilih.',
            'packer2_status.in' => 'Kondisi Packer 2 tidak valid.',
        ]);
    }

    private function resolveMaterialUnit(string $materialName): string
    {
        return in_array($materialName, ['Solar', 'Gas']) ? 'liter' : 'ton';
    }

    private function normalizeNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim((string) $value);
        $value = str_replace(' ', '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : 0;
    }

    private function isEmptyDetailValue($originalValue, float $normalizedValue): bool
    {
        return $originalValue === null || $originalValue === '' || $normalizedValue <= 0;
    }
}

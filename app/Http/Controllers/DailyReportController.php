<?php

namespace App\Http\Controllers;

use App\Models\BagStock;
use App\Models\DailyReport;
use App\Models\MaterialIntransit;
use App\Models\MaterialReceipt;
use App\Models\MaterialStock;
use App\Models\MaterialUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyReport::query();

        if ($request->filled('report_date')) {
            $query->whereDate('report_date', $request->report_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('report_date', (int) $request->month);

            if ($request->filled('year')) {
                $query->whereYear('report_date', (int) $request->year);
            }
        }

        if ($request->filled('weekday')) {
            // weekday: 1 = Senin, 2 = Selasa, ..., 7 = Minggu
            $query->whereRaw('WEEKDAY(report_date) + 1 = ?', [(int) $request->weekday]);
        }

        if ($request->filled('status')) {
            $query->where(function ($q) use ($request) {
                $q->where('cement_mill_status', $request->status)
                    ->orWhere('packer1_status', $request->status)
                    ->orWhere('packer2_status', $request->status);
            });
        }

        $reports = $query
            ->latest('report_date')
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

        return view('reports.show', compact('report'));
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
        });

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan harian berhasil diperbarui.');
    }

    public function destroy(DailyReport $report)
    {
        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan harian berhasil dihapus.');
    }

    private function saveDetailData(DailyReport $report, Request $request): void
    {
        $report->materialStocks()->delete();
        $report->materialReceipts()->delete();
        $report->materialUsages()->delete();
        $report->materialIntransits()->delete();
        $report->bagStocks()->delete();

        foreach ($request->stocks ?? [] as $materialName => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            MaterialStock::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => $materialName === 'Solar' ? 'liter' : 'ton',
            ]);
        }

        foreach ($request->receipts ?? [] as $materialName => $quantity) {
            $normalizedQuantity = $this->normalizeNumber($quantity);

            if ($this->isEmptyDetailValue($quantity, $normalizedQuantity)) {
                continue;
            }

            MaterialReceipt::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $normalizedQuantity,
                'unit' => 'ton',
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
                'unit' => in_array($materialName, ['Solar', 'Gas']) ? 'liter' : 'ton',
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
                'unit' => 'ton',
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

            'feed' => 'nullable|string',
            'blaine' => 'nullable|string',
            'sieving' => 'nullable|string',
            'production_cm' => 'nullable|string',
            'production_ship' => 'nullable|string',
            'running_hours' => 'nullable|string',
            'clinker_factor' => 'nullable|string',
            'silo_semen' => 'nullable|string',

            'truck_packer_area' => 'nullable|string',
            'truck_emplacement_area' => 'nullable|string',
            'production_packer' => 'nullable|string',

            'stocks' => 'nullable|array',
            'stocks.*' => 'nullable|string',

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

    public function exportExcel(DailyReport $report)
    {
        $report->load([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'materialIntransits',
            'bagStocks',
        ]);

        $date = \Carbon\Carbon::parse($report->report_date)->format('Y-m-d');
        $filename = 'laporan-harian-' . $date . '.xls';

        $html = view('exports.daily-report-excel', compact('report'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
    public function exportMonthlyExcel(Request $request)
    {
        $query = DailyReport::with([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'materialIntransits',
            'bagStocks',
        ]);

        $filterTitle = 'Semua Laporan';

        if ($request->filled('month')) {
            $query->whereMonth('report_date', (int) $request->month);

            if ($request->filled('year')) {
                $query->whereYear('report_date', (int) $request->year);
            }

            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $filterTitle = 'Bulan ' . ($monthNames[(int) $request->month] ?? '-');

            if ($request->filled('year')) {
                $filterTitle .= ' ' . $request->year;
            }
        }

        if ($request->filled('weekday')) {
            $query->whereRaw('WEEKDAY(report_date) + 1 = ?', [(int) $request->weekday]);

            $dayNames = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu',
            ];

            $filterTitle .= ' - Hari ' . ($dayNames[(int) $request->weekday] ?? '-');
        }

        if ($request->filled('status')) {
            $query->where(function ($q) use ($request) {
                $q->where('cement_mill_status', $request->status)
                    ->orWhere('packer1_status', $request->status)
                    ->orWhere('packer2_status', $request->status);
            });

            $filterTitle .= ' - Status ' . $request->status;
        }

        if ($request->filled('report_date')) {
            $query->whereDate('report_date', $request->report_date);

            $filterTitle = 'Tanggal ' . \Carbon\Carbon::parse($request->report_date)->format('d-m-Y');
        }

        $reports = $query->orderBy('report_date', 'asc')->get();

        $filename = 'rekap-laporan-' . now()->format('Ymd-His') . '.xls';

        $html = view('exports.monthly-report-excel', compact('reports', 'filterTitle'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BagStock;
use App\Models\DailyReport;
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
                'feed' => $request->feed ?? 0,
                'blaine' => $request->blaine ?? 0,
                'sieving' => $request->sieving ?? 0,
                'production_cm' => $request->production_cm ?? 0,
                'running_hours' => $request->running_hours ?? 0,
                'clinker_factor' => $request->clinker_factor ?? 0,
                'silo_semen' => $request->silo_semen ?? 0,
                'packer1_status' => $request->packer1_status,
                'packer1_note' => $request->packer1_note,
                'packer2_status' => $request->packer2_status,
                'packer2_note' => $request->packer2_note,
                'truck_packer_area' => $request->truck_packer_area ?? 0,
                'truck_emplacement_area' => $request->truck_emplacement_area ?? 0,
                'production_packer' => $request->production_packer ?? 0,
                'created_by' => Auth::id(),
            ]);

            $this->saveDetailData($report, $request);
        });

        return redirect()->route('reports.index')->with('success', 'Laporan harian berhasil disimpan.');
    }

    public function show(DailyReport $report)
    {
        $report->load([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'bagStocks'
        ]);

        return view('reports.show', compact('report'));
    }

    public function edit(DailyReport $report)
    {
        $report->load([
            'materialStocks',
            'materialReceipts',
            'materialUsages',
            'bagStocks'
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
                'feed' => $request->feed ?? 0,
                'blaine' => $request->blaine ?? 0,
                'sieving' => $request->sieving ?? 0,
                'production_cm' => $request->production_cm ?? 0,
                'running_hours' => $request->running_hours ?? 0,
                'clinker_factor' => $request->clinker_factor ?? 0,
                'silo_semen' => $request->silo_semen ?? 0,
                'packer1_status' => $request->packer1_status,
                'packer1_note' => $request->packer1_note,
                'packer2_status' => $request->packer2_status,
                'packer2_note' => $request->packer2_note,
                'truck_packer_area' => $request->truck_packer_area ?? 0,
                'truck_emplacement_area' => $request->truck_emplacement_area ?? 0,
                'production_packer' => $request->production_packer ?? 0,
            ]);

            $report->materialStocks()->delete();
            $report->materialReceipts()->delete();
            $report->materialUsages()->delete();
            $report->bagStocks()->delete();

            $this->saveDetailData($report, $request);
        });

        return redirect()->route('reports.index')->with('success', 'Laporan harian berhasil diperbarui.');
    }

    public function destroy(DailyReport $report)
    {
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Laporan harian berhasil dihapus.');
    }

    private function saveDetailData(DailyReport $report, Request $request)
    {
        foreach ($request->stocks ?? [] as $materialName => $quantity) {
            MaterialStock::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $quantity ?? 0,
                'unit' => $materialName === 'Solar' ? 'liter' : 'ton',
            ]);
        }

        foreach ($request->receipts ?? [] as $materialName => $quantity) {
            MaterialReceipt::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $quantity ?? 0,
                'unit' => $materialName === 'Solar' ? 'liter' : 'ton',
            ]);
        }

        foreach ($request->usages ?? [] as $materialName => $quantity) {
            MaterialUsage::create([
                'daily_report_id' => $report->id,
                'material_name' => $materialName,
                'quantity' => $quantity ?? 0,
                'unit' => $materialName === 'Solar' || $materialName === 'Gas' ? 'liter' : 'ton',
            ]);
        }

        foreach ($request->bags ?? [] as $bagType => $quantity) {
            BagStock::create([
                'daily_report_id' => $report->id,
                'bag_type' => $bagType,
                'quantity' => $quantity ?? 0,
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

            'feed' => 'nullable|numeric|min:0',
            'blaine' => 'nullable|numeric|min:0',
            'sieving' => 'nullable|numeric|min:0',
            'production_cm' => 'nullable|numeric|min:0',
            'running_hours' => 'nullable|numeric|min:0',
            'clinker_factor' => 'nullable|numeric|min:0',
            'silo_semen' => 'nullable|numeric|min:0',

            'truck_packer_area' => 'nullable|integer|min:0',
            'truck_emplacement_area' => 'nullable|integer|min:0',
            'production_packer' => 'nullable|numeric|min:0',

            'stocks' => 'nullable|array',
            'stocks.*' => 'nullable|numeric|min:0',

            'receipts' => 'nullable|array',
            'receipts.*' => 'nullable|numeric|min:0',

            'usages' => 'nullable|array',
            'usages.*' => 'nullable|numeric|min:0',

            'bags' => 'nullable|array',
            'bags.*' => 'nullable|numeric|min:0',
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

            '*.numeric' => 'Input angka harus berupa angka yang valid.',
            '*.min' => 'Input angka tidak boleh bernilai minus.',

            'truck_packer_area.integer' => 'Jumlah truk area packer harus berupa bilangan bulat.',
            'truck_emplacement_area.integer' => 'Jumlah truk area emplacement harus berupa bilangan bulat.',
        ]);
    }
}

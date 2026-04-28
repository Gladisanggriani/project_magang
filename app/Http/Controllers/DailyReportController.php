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
    public function index()
    {
        $reports = DailyReport::latest('report_date')->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date|unique:daily_reports,report_date',
        ]);

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
}
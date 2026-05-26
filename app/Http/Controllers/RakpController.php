<?php

namespace App\Http\Controllers;

use App\Models\Rakp;
use Illuminate\Http\Request;

class RakpController extends Controller
{
    private array $monthNames = [
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

    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $cementMillRakps = Rakp::where('year', $year)
            ->where('material_name', 'Cement Mill')
            ->get()
            ->keyBy('month');

        $packerRakps = Rakp::where('year', $year)
            ->where('material_name', 'Packer')
            ->get()
            ->keyBy('month');

        $totalCementMill = $cementMillRakps->sum('value');
        $totalPacker = $packerRakps->sum('value');
        $grandTotal = $totalCementMill + $totalPacker;

        return view('rakps.index', [
            'year' => $year,
            'monthNames' => $this->monthNames,
            'cementMillRakps' => $cementMillRakps,
            'packerRakps' => $packerRakps,
            'totalCementMill' => $totalCementMill,
            'totalPacker' => $totalPacker,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'cement_mill' => ['nullable', 'array'],
            'packer' => ['nullable', 'array'],
        ]);

        $year = (int) $request->year;

        $this->saveRakpGroup($year, 'Cement Mill', $request->input('cement_mill', []));
        $this->saveRakpGroup($year, 'Packer', $request->input('packer', []));

        return redirect()
            ->route('rakps.index', ['year' => $year])
            ->with('success', 'Data RKAP berhasil disimpan.');
    }

    private function saveRakpGroup(int $year, string $materialName, array $values): void
    {
        foreach ($this->monthNames as $month => $monthName) {
            $rawValue = $values[$month] ?? null;
            $value = $this->normalizeNumber($rawValue);

            Rakp::updateOrCreate(
                [
                    'year' => $year,
                    'month' => $month,
                    'material_name' => $materialName,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }

    private function normalizeNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim((string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0;
    }

    public function export(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $cementMillRakps = Rakp::where('year', $year)
            ->where('material_name', 'Cement Mill')
            ->get()
            ->keyBy('month');

        $packerRakps = Rakp::where('year', $year)
            ->where('material_name', 'Packer')
            ->get()
            ->keyBy('month');

        $totalCementMill = $cementMillRakps->sum('value');
        $totalPacker = $packerRakps->sum('value');
        $grandTotal = $totalCementMill + $totalPacker;

        $filename = 'rekap-rkap-' . $year . '-' . now()->format('Ymd-His') . '.xls';

        return response()
            ->view('exports.rkap-excel', [
                'year' => $year,
                'monthNames' => $this->monthNames,
                'cementMillRakps' => $cementMillRakps,
                'packerRakps' => $packerRakps,
                'totalCementMill' => $totalCementMill,
                'totalPacker' => $totalPacker,
                'grandTotal' => $grandTotal,
            ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}

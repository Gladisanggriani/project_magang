<?php

namespace App\Http\Controllers;

use App\Models\Rakp;
use Illuminate\Http\Request;

class RakpController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $materialName = 'Semen';

        $monthNames = $this->monthNames();

        $rakps = Rakp::where('year', $year)
            ->where('material_name', $materialName)
            ->get()
            ->keyBy('month');

        return view('rakps.index', compact(
            'year',
            'materialName',
            'monthNames',
            'rakps'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'rakps' => 'nullable|array',
            'rakps.*' => 'nullable|string',
        ]);

        $year = (int) $request->year;
        $materialName = 'Semen';

        foreach ($request->rakps ?? [] as $month => $value) {
            $month = (int) $month;
            $normalizedValue = $this->normalizeNumber($value);

            if ($value === null || $value === '' || $normalizedValue <= 0) {
                Rakp::where('year', $year)
                    ->where('month', $month)
                    ->where('material_name', $materialName)
                    ->delete();

                continue;
            }

            Rakp::updateOrCreate(
                [
                    'year' => $year,
                    'month' => $month,
                    'material_name' => $materialName,
                ],
                [
                    'value' => $normalizedValue,
                ]
            );
        }

        return redirect()
            ->route('rakps.index', ['year' => $year])
            ->with('success', 'Data RAKP berhasil disimpan.');
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

    private function monthNames(): array
    {
        return [
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
    }
}
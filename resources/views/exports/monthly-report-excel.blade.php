<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="10" style="font-size:18px;text-align:center;">
                    REKAP LAPORAN HARIAN
                </th>
            </tr>
            <tr>
                <th colspan="10" style="text-align:center;">
                    PT Semen Padang Unit Pabrik Dumai
                </th>
            </tr>
            <tr>
                <th colspan="10" style="text-align:center;">
                    {{ $filterTitle }}
                </th>
            </tr>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Produksi Semen</th>
                <th>Produksi Kapal</th>
                <th>Produksi Packer</th>
                <th>Silo Semen</th>
                <th>Closing Stock</th>
                <th>Status Mill</th>
                <th>Total Antrian</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalProductionCm = 0;
                $totalProductionShip = 0;
                $totalProductionPacker = 0;
            @endphp

            @forelse ($reports as $index => $report)
                @php
                    $closingStock = ($report->silo_semen ?? 0)
                        + ($report->production_cm ?? 0)
                        - ($report->production_packer ?? 0);

                    $totalTruck = ($report->truck_packer_area ?? 0)
                        + ($report->truck_emplacement_area ?? 0);

                    $totalProductionCm += $report->production_cm ?? 0;
                    $totalProductionShip += $report->production_ship ?? 0;
                    $totalProductionPacker += $report->production_packer ?? 0;
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l') }}</td>
                    <td>{{ number_format($report->production_cm ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($report->production_ship ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($report->production_packer ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($report->silo_semen ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($closingStock, 2, ',', '.') }}</td>
                    <td>{{ $report->cement_mill_status }}</td>
                    <td>{{ $totalTruck }} Truck</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">Tidak ada data laporan.</td>
                </tr>
            @endforelse

            <tr>
                <th colspan="3">Total</th>
                <th>{{ number_format($totalProductionCm, 2, ',', '.') }}</th>
                <th>{{ number_format($totalProductionShip, 2, ',', '.') }}</th>
                <th>{{ number_format($totalProductionPacker, 2, ',', '.') }}</th>
                <th colspan="4"></th>
            </tr>
        </tbody>
    </table>
</body>
</html>
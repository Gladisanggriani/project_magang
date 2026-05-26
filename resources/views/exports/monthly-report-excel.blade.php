<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan Bulanan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .title-main {
            background: #c5161d;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .title-sub {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .title-filter {
            font-size: 12px;
            text-align: center;
        }

        .section-title {
            background: #f8d7da;
            color: #9f1239;
            font-weight: bold;
            text-align: left;
        }

        .table-header {
            background: #f8d7da;
            color: #9f1239;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .total-row {
            background: #f1f5f9;
            font-weight: bold;
        }

        .empty-row {
            text-align: center;
            color: #64748b;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="10" class="title-main">REKAP LAPORAN BULANAN</th>
        </tr>
        <tr>
            <th colspan="10" class="title-sub">PT Semen Padang Unit Pabrik Dumai</th>
        </tr>
        <tr>
            <th colspan="10" class="title-filter">{{ $filterTitle }}</th>
        </tr>

        <tr>
            <td colspan="10"></td>
        </tr>

        <tr>
            <th colspan="10" class="section-title">INFORMASI REKAP LAPORAN</th>
        </tr>
        <tr>
            <th class="table-header">No</th>
            <th class="table-header">Tanggal</th>
            <th class="table-header">Hari</th>
            <th class="table-header">Produksi Semen</th>
            <th class="table-header">Produksi dari Kapal</th>
            <th class="table-header">Produksi Packer</th>
            <th class="table-header">Silo Semen</th>
            <th class="table-header">Closing Stock</th>
            <th class="table-header">Status Mill</th>
            <th class="table-header">Total Antrian</th>
        </tr>

        @php
            $totalProductionCm = 0;
            $totalProductionShip = 0;
            $totalProductionPacker = 0;
            $totalSiloSemen = 0;
            $totalClosingStock = 0;
            $totalTruckAll = 0;
        @endphp

        @forelse ($reports as $index => $report)
            @php
                $closingStock =
                    ($report->silo_semen ?? 0)
                    + ($report->production_cm ?? 0)
                    - ($report->production_packer ?? 0);

                $totalTruck =
                    ($report->truck_packer_area ?? 0)
                    + ($report->truck_emplacement_area ?? 0);

                $totalProductionCm += $report->production_cm ?? 0;
                $totalProductionShip += $report->production_ship ?? 0;
                $totalProductionPacker += $report->production_packer ?? 0;
                $totalSiloSemen += $report->silo_semen ?? 0;
                $totalClosingStock += $closingStock;
                $totalTruckAll += $totalTruck;
            @endphp

            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l') }}</td>
                <td class="text-right">{{ number_format($report->production_cm ?? 0, 2, ',', '.') }} Ton</td>
                <td class="text-right">{{ number_format($report->production_ship ?? 0, 2, ',', '.') }} Ton</td>
                <td class="text-right">{{ number_format($report->production_packer ?? 0, 2, ',', '.') }} Ton</td>
                <td class="text-right">{{ number_format($report->silo_semen ?? 0, 2, ',', '.') }} Ton</td>
                <td class="text-right">{{ number_format($closingStock, 2, ',', '.') }} Ton</td>
                <td class="text-center">{{ $report->cement_mill_status ?: '-' }}</td>
                <td class="text-center">{{ $totalTruck }} Truck</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="empty-row">Tidak ada data laporan.</td>
            </tr>
        @endforelse

        <tr class="total-row">
            <td colspan="3" class="text-center">TOTAL</td>
            <td class="text-right">{{ number_format($totalProductionCm, 2, ',', '.') }} Ton</td>
            <td class="text-right">{{ number_format($totalProductionShip, 2, ',', '.') }} Ton</td>
            <td class="text-right">{{ number_format($totalProductionPacker, 2, ',', '.') }} Ton</td>
            <td class="text-right">{{ number_format($totalSiloSemen, 2, ',', '.') }} Ton</td>
            <td class="text-right">{{ number_format($totalClosingStock, 2, ',', '.') }} Ton</td>
            <td class="text-center">-</td>
            <td class="text-center">{{ $totalTruckAll }} Truck</td>
        </tr>
    </table>
</body>
</html>
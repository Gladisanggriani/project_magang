<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        td, th {
            border: 1px solid #000000;
            padding: 6px;
            vertical-align: middle;
        }

        .title {
            background-color: #b91c1c;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .subtitle {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .section {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }

        .header {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .no-border td {
            border: none;
        }
    </style>
</head>
<body>
<table>
    <tr>
        <td colspan="4" class="title">LAPORAN NUANSA HARIAN</td>
    </tr>
    <tr>
        <td colspan="4" class="subtitle">PT Semen Padang Unit Pabrik Dumai</td>
    </tr>
    <tr>
        <td colspan="4" class="center">
            Tanggal Laporan:
            {{ \Carbon\Carbon::parse($report->report_date)->format('d F Y') }}
        </td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- INFORMASI LAPORAN --}}
    <tr>
        <td colspan="4" class="section">INFORMASI LAPORAN</td>
    </tr>
    <tr>
        <td class="bold">Tanggal</td>
        <td colspan="3">{{ \Carbon\Carbon::parse($report->report_date)->format('d F Y') }}</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- CEMENT MILL --}}
    <tr>
        <td colspan="4" class="section">1. CEMENT MILL DUMAI</td>
    </tr>
    <tr class="header">
        <td>Parameter</td>
        <td>Nilai</td>
        <td>Satuan</td>
        <td>Keterangan</td>
    </tr>
    <tr>
        <td>Operational Cement Mill</td>
        <td>{{ $report->cement_mill_status }}</td>
        <td>-</td>
        <td>{{ $report->cement_mill_note ?: '-' }}</td>
    </tr>
    <tr>
        <td>Feed</td>
        <td class="right">{{ number_format($report->feed, 2, ',', '.') }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Blaine</td>
        <td class="right">{{ number_format($report->blaine, 2, ',', '.') }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Sieving</td>
        <td class="right">{{ number_format($report->sieving, 2, ',', '.') }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Produksi 00.00 s/d 07.00</td>
        <td class="right">{{ number_format($report->production_cm, 2, ',', '.') }}</td>
        <td>Ton</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Running Hours</td>
        <td class="right">{{ number_format($report->running_hours, 2, ',', '.') }}</td>
        <td>Jam</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Clinker Factor</td>
        <td class="right">{{ number_format($report->clinker_factor, 2, ',', '.') }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Silo Semen</td>
        <td class="right">{{ number_format($report->silo_semen, 2, ',', '.') }}</td>
        <td>Ton</td>
        <td>-</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- STOCK MATERIAL --}}
    <tr>
        <td colspan="4" class="section">2. STOCK MATERIAL</td>
    </tr>
    <tr class="header">
        <td>No</td>
        <td>Material</td>
        <td>Qty</td>
        <td>Satuan</td>
    </tr>
    @forelse($report->materialStocks as $index => $stock)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $stock->material_name }}</td>
            <td class="right">{{ number_format($stock->quantity, 2, ',', '.') }}</td>
            <td>{{ $stock->unit }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">Belum ada data stock material.</td>
        </tr>
    @endforelse

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- PENERIMAAN MATERIAL --}}
    <tr>
        <td colspan="4" class="section">3. PENERIMAAN MATERIAL 00.00 s/d 24.00</td>
    </tr>
    <tr class="header">
        <td>No</td>
        <td>Material</td>
        <td>Qty</td>
        <td>Satuan</td>
    </tr>
    @forelse($report->materialReceipts as $index => $receipt)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $receipt->material_name }}</td>
            <td class="right">{{ number_format($receipt->quantity, 2, ',', '.') }}</td>
            <td>{{ $receipt->unit }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">Belum ada data penerimaan material.</td>
        </tr>
    @endforelse

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- PEMAKAIAN MATERIAL --}}
    <tr>
        <td colspan="4" class="section">4. PEMAKAIAN MATERIAL 00.00 s/d 24.00</td>
    </tr>
    <tr class="header">
        <td>No</td>
        <td>Material</td>
        <td>Qty</td>
        <td>Satuan</td>
    </tr>
    @forelse($report->materialUsages as $index => $usage)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $usage->material_name }}</td>
            <td class="right">{{ number_format($usage->quantity, 2, ',', '.') }}</td>
            <td>{{ $usage->unit }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">Belum ada data pemakaian material.</td>
        </tr>
    @endforelse

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- PACKER DUMAI --}}
    <tr>
        <td colspan="4" class="section">5. PACKER DUMAI</td>
    </tr>
    <tr class="header">
        <td>Peralatan</td>
        <td>Kondisi</td>
        <td colspan="2">Keterangan</td>
    </tr>
    <tr>
        <td>Packer 1</td>
        <td>{{ $report->packer1_status }}</td>
        <td colspan="2">{{ $report->packer1_note ?: '-' }}</td>
    </tr>
    <tr>
        <td>Packer 2</td>
        <td>{{ $report->packer2_status }}</td>
        <td colspan="2">{{ $report->packer2_note ?: '-' }}</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- ANTRIAN TRUK --}}
    <tr>
        <td colspan="4" class="section">6. ANTRIAN TRUK</td>
    </tr>
    <tr>
        <td>Area Packer</td>
        <td class="right">{{ $report->truck_packer_area }}</td>
        <td colspan="2">Truck</td>
    </tr>
    <tr>
        <td>Area Emplacement</td>
        <td class="right">{{ $report->truck_emplacement_area }}</td>
        <td colspan="2">Truck</td>
    </tr>
    <tr>
        <td>Total Antrian</td>
        <td class="right">{{ ($report->truck_packer_area ?? 0) + ($report->truck_emplacement_area ?? 0) }}</td>
        <td colspan="2">Truck</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- PRODUKSI PACKER --}}
    <tr>
        <td colspan="4" class="section">7. PRODUKSI PACKER</td>
    </tr>
    <tr>
        <td>Total Produksi Packer</td>
        <td class="right">{{ number_format($report->production_packer, 2, ',', '.') }}</td>
        <td colspan="2">Ton</td>
    </tr>

    <tr>
        <td colspan="4"></td>
    </tr>

    {{-- STOCK KANTONG --}}
    <tr>
        <td colspan="4" class="section">8. STOCK KANTONG</td>
    </tr>
    <tr class="header">
        <td>No</td>
        <td>Jenis Kantong</td>
        <td>Qty</td>
        <td>Satuan</td>
    </tr>
    @forelse($report->bagStocks as $index => $bag)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $bag->bag_type }}</td>
            <td class="right">{{ number_format($bag->quantity, 0, ',', '.') }}</td>
            <td>{{ $bag->unit }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">Belum ada data stock kantong.</td>
        </tr>
    @endforelse
</table>
</body>
</html>
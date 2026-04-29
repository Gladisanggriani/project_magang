@extends('layouts.main')

@section('title', 'Detail Laporan')

@section('content')
<div class="page-card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
        <div>
            <h2 class="page-card-title">Detail Laporan Nuansa Harian</h2>
            <p class="page-card-subtitle">
                Tanggal {{ \Carbon\Carbon::parse($report->report_date)->format('d F Y') }}
            </p>
        </div>

        <div class="form-actions">
            <a href="{{ route('reports.edit', $report->id) }}" class="btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Laporan
            </a>

            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="data-grid">
    <div class="page-card data-col-4">
        <h3 class="panel-title" style="margin-bottom:16px;">1. Cement Mill Dumai</h3>
        <table class="info-table">
            <tr><td>Operational Cement Mill</td><td>{{ $report->cement_mill_status }}</td></tr>
            <tr><td>Status Cement Mill</td><td>{{ $report->cement_mill_note ?: '-' }}</td></tr>
            <tr><td>Feed</td><td>{{ number_format($report->feed, 2, ',', '.') }}</td></tr>
            <tr><td>Blaine</td><td>{{ number_format($report->blaine, 2, ',', '.') }}</td></tr>
            <tr><td>Sieving</td><td>{{ number_format($report->sieving, 2, ',', '.') }}</td></tr>
            <tr><td>Produksi 00.00 s/d 07.00</td><td>{{ number_format($report->production_cm, 2, ',', '.') }}</td></tr>
            <tr><td>Running Hours</td><td>{{ number_format($report->running_hours, 2, ',', '.') }}</td></tr>
            <tr><td>Clinker Factor</td><td>{{ number_format($report->clinker_factor, 2, ',', '.') }}</td></tr>
            <tr><td>Silo Semen</td><td>{{ number_format($report->silo_semen, 2, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="page-card data-col-4">
        <h3 class="panel-title" style="margin-bottom:16px;">5. Packer Dumai</h3>
        <table class="info-table">
            <tr><td>Packer 1</td><td>{{ $report->packer1_status }}</td></tr>
            <tr><td>Keterangan Packer 1</td><td>{{ $report->packer1_note ?: '-' }}</td></tr>
            <tr><td>Packer 2</td><td>{{ $report->packer2_status }}</td></tr>
            <tr><td>Keterangan Packer 2</td><td>{{ $report->packer2_note ?: '-' }}</td></tr>
        </table>
    </div>

    <div class="page-card data-col-4">
        <h3 class="panel-title" style="margin-bottom:16px;">6. Antrian Truk</h3>
        <table class="info-table">
            <tr><td>Area Packer</td><td>{{ $report->truck_packer_area }}</td></tr>
            <tr><td>Area Emplacement</td><td>{{ $report->truck_emplacement_area }}</td></tr>
            <tr><td>Total Antrian</td><td>{{ $report->truck_packer_area + $report->truck_emplacement_area }}</td></tr>
        </table>
    </div>

    <div class="page-card data-col-4">
        <h3 class="panel-title" style="margin-bottom:16px;">2. Stock Material</h3>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report->materialStocks as $stock)
                    <tr>
                        <td>{{ $stock->material_name }}</td>
                        <td>{{ number_format($stock->quantity, 2, ',', '.') }} {{ $stock->unit }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card data-col-4">
        <h3 class="panel-title" style="margin-bottom:16px;">3. Penerimaan Material</h3>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report->materialReceipts as $receipt)
                    <tr>
                        <td>{{ $receipt->material_name }}</td>
                        <td>{{ number_format($receipt->quantity, 2, ',', '.') }} {{ $receipt->unit }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card data-col-4">
        <h3 class="panel-title" style="margin-bottom:16px;">4. Pemakaian Material</h3>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report->materialUsages as $usage)
                    <tr>
                        <td>{{ $usage->material_name }}</td>
                        <td>{{ number_format($usage->quantity, 2, ',', '.') }} {{ $usage->unit }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card data-col-6">
        <h3 class="panel-title" style="margin-bottom:16px;">7. Produksi Packer</h3>
        <table class="info-table">
            <tr>
                <td>Total Produksi Packer</td>
                <td>{{ number_format($report->production_packer, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="page-card data-col-6">
        <h3 class="panel-title" style="margin-bottom:16px;">Stock Kantong</h3>

        <div class="metric-stack">
            @forelse($report->bagStocks as $bag)
                <div class="metric-row">
                    <span class="metric-label">{{ $bag->bag_type }}</span>
                    <span class="metric-value">{{ number_format($bag->quantity, 0, ',', '.') }} {{ $bag->unit }}</span>
                </div>
            @empty
                <div class="metric-row">
                    <span class="metric-label">Belum ada data stock kantong</span>
                    <span class="metric-value">-</span>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
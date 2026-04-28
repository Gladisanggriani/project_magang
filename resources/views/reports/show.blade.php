@extends('layouts.main')

@section('title', 'Detail Laporan')

@section('content')
    <div class="page-card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 class="page-card-title">Detail Laporan</h2>
                <p class="page-card-subtitle">
                    Tanggal {{ \Carbon\Carbon::parse($report->report_date)->format('d F Y') }}
                </p>
            </div>

            <div class="form-actions">
                @if (auth()->user()->hasRole(['admin', 'operator']))
                    <a href="{{ route('reports.edit', $report->id) }}" class="btn-primary">
                        <i class="bi bi-pencil-square"></i> Edit Laporan
                    </a>
                @endif

                <a href="{{ route('reports.index') }}" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="data-grid">
        <div class="page-card data-col-4">
            <h3 class="panel-title" style="margin-bottom:16px;">Cement Mill</h3>
            <table class="info-table">
                <tr>
                    <td>Status</td>
                    <td>{{ $report->cement_mill_status }}</td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td>{{ $report->cement_mill_note ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Feed</td>
                    <td>{{ $report->feed }}</td>
                </tr>
                <tr>
                    <td>Blaine</td>
                    <td>{{ $report->blaine }}</td>
                </tr>
                <tr>
                    <td>Sieving</td>
                    <td>{{ $report->sieving }}</td>
                </tr>
                <tr>
                    <td>Produksi CM</td>
                    <td>{{ $report->production_cm }}</td>
                </tr>
                <tr>
                    <td>Running Hours</td>
                    <td>{{ $report->running_hours }}</td>
                </tr>
                <tr>
                    <td>Clinker Factor</td>
                    <td>{{ $report->clinker_factor }}</td>
                </tr>
                <tr>
                    <td>Silo Semen</td>
                    <td>{{ $report->silo_semen }}</td>
                </tr>
            </table>
        </div>

        <div class="page-card data-col-4">
            <h3 class="panel-title" style="margin-bottom:16px;">Packer</h3>
            <table class="info-table">
                <tr>
                    <td>Status Packer 1</td>
                    <td>{{ $report->packer1_status }}</td>
                </tr>
                <tr>
                    <td>Keterangan Packer 1</td>
                    <td>{{ $report->packer1_note ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Status Packer 2</td>
                    <td>{{ $report->packer2_status }}</td>
                </tr>
                <tr>
                    <td>Keterangan Packer 2</td>
                    <td>{{ $report->packer2_note ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Produksi Packer</td>
                    <td>{{ $report->production_packer }}</td>
                </tr>
            </table>
        </div>

        <div class="page-card data-col-4">
            <h3 class="panel-title" style="margin-bottom:16px;">Antrian Truck</h3>
            <table class="info-table">
                <tr>
                    <td>Area Packer</td>
                    <td>{{ $report->truck_packer_area }}</td>
                </tr>
                <tr>
                    <td>Area Emplacement</td>
                    <td>{{ $report->truck_emplacement_area }}</td>
                </tr>
            </table>
        </div>

        <div class="page-card data-col-4">
            <h3 class="panel-title" style="margin-bottom:16px;">Closing Stock</h3>
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
                            <td>{{ $stock->quantity }} {{ $stock->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="page-card data-col-4">
            <h3 class="panel-title" style="margin-bottom:16px;">Penerimaan Material</h3>
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
                            <td>{{ $receipt->quantity }} {{ $receipt->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="page-card data-col-4">
            <h3 class="panel-title" style="margin-bottom:16px;">Pemakaian Material</h3>
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
                            <td>{{ $usage->quantity }} {{ $usage->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="page-card data-col-12">
            <h3 class="panel-title" style="margin-bottom:16px;">Stock Kantong</h3>

            <div class="metric-stack">
                @forelse($report->bagStocks as $bag)
                    <div class="metric-row">
                        <span class="metric-label">{{ $bag->bag_type }}</span>
                        <span class="metric-value">{{ number_format($bag->quantity, 0, ',', '.') }}
                            {{ $bag->unit }}</span>
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

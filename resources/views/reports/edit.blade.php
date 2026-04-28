@extends('layouts.main')

@section('title', 'Edit Laporan Harian')

@section('content')
@php
    $stockValue = fn($name) => optional($report->materialStocks->firstWhere('material_name', $name))->quantity ?? 0;
    $receiptValue = fn($name) => optional($report->materialReceipts->firstWhere('material_name', $name))->quantity ?? 0;
    $usageValue = fn($name) => optional($report->materialUsages->firstWhere('material_name', $name))->quantity ?? 0;
    $bagValue = fn($name) => optional($report->bagStocks->firstWhere('bag_type', $name))->quantity ?? 0;
@endphp

<div class="page-card">
    <h2 class="page-card-title">Edit Laporan Harian</h2>
    <p class="page-card-subtitle">
        Perbarui data laporan operasional. Setelah disimpan, data pada dashboard akan ikut berubah.
    </p>
</div>

@if($errors->any())
    <div class="alert-danger">
        <strong>Terjadi kesalahan:</strong>
        <ul style="margin:10px 0 0 18px;">
            @foreach($errors->all() as $error)
                <li style="margin-bottom:4px;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('reports.update', $report->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-calendar2-check"></i>
            </div>
            <span>Informasi Laporan</span>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Tanggal Laporan</label>
                <input type="date" name="report_date" value="{{ old('report_date', $report->report_date) }}" required>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
            <span>Cement Mill Dumai</span>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Status Cement Mill</label>
                <select name="cement_mill_status">
                    @foreach(['RUN', 'STOP', 'MAINTENANCE', 'TROUBLE'] as $status)
                        <option value="{{ $status }}" @selected(old('cement_mill_status', $report->cement_mill_status) == $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Cement Mill</label>
                <input type="text" name="cement_mill_note" value="{{ old('cement_mill_note', $report->cement_mill_note) }}">
            </div>

            <div class="form-group">
                <label>Feed</label>
                <input type="number" step="0.01" name="feed" value="{{ old('feed', $report->feed) }}">
            </div>

            <div class="form-group">
                <label>Blaine</label>
                <input type="number" step="0.01" name="blaine" value="{{ old('blaine', $report->blaine) }}">
            </div>

            <div class="form-group">
                <label>Sieving</label>
                <input type="number" step="0.01" name="sieving" value="{{ old('sieving', $report->sieving) }}">
            </div>

            <div class="form-group">
                <label>Produksi Cement Mill</label>
                <input type="number" step="0.01" name="production_cm" value="{{ old('production_cm', $report->production_cm) }}">
            </div>

            <div class="form-group">
                <label>Running Hours</label>
                <input type="number" step="0.01" name="running_hours" value="{{ old('running_hours', $report->running_hours) }}">
            </div>

            <div class="form-group">
                <label>Clinker Factor</label>
                <input type="number" step="0.01" name="clinker_factor" value="{{ old('clinker_factor', $report->clinker_factor) }}">
            </div>

            <div class="form-group">
                <label>Silo Semen</label>
                <input type="number" step="0.01" name="silo_semen" value="{{ old('silo_semen', $report->silo_semen) }}">
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <span>Closing Stock Material</span>
        </div>

        <div class="form-grid">
            @foreach(['Semen', 'Klinker', 'Gypsum Natural', 'Gypsum Purified', 'Pozzolan', 'Wet Fly Ash', 'Dry Fly Ash', 'Limestone', 'Solar'] as $material)
                <div class="form-group">
                    <label>{{ $material }}</label>
                    <input type="number" step="0.01" name="stocks[{{ $material }}]" value="{{ old('stocks.' . $material, $stockValue($material)) }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <span>Penerimaan Material</span>
        </div>

        <div class="form-grid">
            @foreach(['Klinker', 'Gypsum', 'Pozzolan', 'Fly Ash', 'Semen Curah', 'Solar'] as $material)
                <div class="form-group">
                    <label>{{ $material }}</label>
                    <input type="number" step="0.01" name="receipts[{{ $material }}]" value="{{ old('receipts.' . $material, $receiptValue($material)) }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <span>Pemakaian Material</span>
        </div>

        <div class="form-grid">
            @foreach(['Klinker', 'Gypsum', 'Pozzolan', 'Fly Ash', 'Limestone', 'Solar', 'Gas'] as $material)
                <div class="form-group">
                    <label>{{ $material }}</label>
                    <input type="number" step="0.01" name="usages[{{ $material }}]" value="{{ old('usages.' . $material, $usageValue($material)) }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-truck"></i>
            </div>
            <span>Packer Dumai & Antrian Truck</span>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Status Packer 1</label>
                <select name="packer1_status">
                    @foreach(['RUN', 'STOP', 'MAINTENANCE', 'TROUBLE'] as $status)
                        <option value="{{ $status }}" @selected(old('packer1_status', $report->packer1_status) == $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Packer 1</label>
                <input type="text" name="packer1_note" value="{{ old('packer1_note', $report->packer1_note) }}">
            </div>

            <div class="form-group">
                <label>Status Packer 2</label>
                <select name="packer2_status">
                    @foreach(['RUN', 'STOP', 'MAINTENANCE', 'TROUBLE'] as $status)
                        <option value="{{ $status }}" @selected(old('packer2_status', $report->packer2_status) == $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Packer 2</label>
                <input type="text" name="packer2_note" value="{{ old('packer2_note', $report->packer2_note) }}">
            </div>

            <div class="form-group">
                <label>Produksi Packer</label>
                <input type="number" step="0.01" name="production_packer" value="{{ old('production_packer', $report->production_packer) }}">
            </div>

            <div class="form-group">
                <label>Truck Area Packer</label>
                <input type="number" name="truck_packer_area" value="{{ old('truck_packer_area', $report->truck_packer_area) }}">
            </div>

            <div class="form-group">
                <label>Truck Area Emplacement</label>
                <input type="number" name="truck_emplacement_area" value="{{ old('truck_emplacement_area', $report->truck_emplacement_area) }}">
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-archive"></i>
            </div>
            <span>Stock Kantong</span>
        </div>

        <div class="form-grid">
            @foreach(['PCC 40 Kg', 'PCC 50 Kg', 'OPC 50 Kg', 'Big Bag'] as $bag)
                <div class="form-group">
                    <label>{{ $bag }}</label>
                    <input type="number" step="0.01" name="bags[{{ $bag }}]" value="{{ old('bags.' . $bag, $bagValue($bag)) }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>

            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>
    </div>
</form>
@endsection
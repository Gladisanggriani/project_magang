@extends('layouts.main')

@section('title', 'Input Laporan Harian')

@section('content')
<div class="page-card">
    <h2 class="page-card-title">Input Laporan Harian</h2>
    <p class="page-card-subtitle">
        Silakan isi data operasional harian GP Dumai. Setelah disimpan, data akan langsung tampil di dashboard.
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

<form action="{{ route('reports.store') }}" method="POST">
    @csrf

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
                <input type="date" name="report_date" required>
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
                    <option value="RUN">RUN</option>
                    <option value="STOP">STOP</option>
                    <option value="MAINTENANCE">MAINTENANCE</option>
                    <option value="TROUBLE">TROUBLE</option>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Cement Mill</label>
                <input type="text" name="cement_mill_note" placeholder="Contoh: Operasional lancar">
            </div>

            <div class="form-group">
                <label>Feed</label>
                <input type="number" step="0.01" name="feed" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Blaine</label>
                <input type="number" step="0.01" name="blaine" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Sieving</label>
                <input type="number" step="0.01" name="sieving" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Produksi Cement Mill</label>
                <input type="number" step="0.01" name="production_cm" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Running Hours</label>
                <input type="number" step="0.01" name="running_hours" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Clinker Factor</label>
                <input type="number" step="0.01" name="clinker_factor" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Silo Semen</label>
                <input type="number" step="0.01" name="silo_semen" placeholder="0.00">
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
                    <input type="number" step="0.01" name="stocks[{{ $material }}]" placeholder="0.00">
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
                    <input type="number" step="0.01" name="receipts[{{ $material }}]" placeholder="0.00">
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
                    <input type="number" step="0.01" name="usages[{{ $material }}]" placeholder="0.00">
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
                    <option value="RUN">RUN</option>
                    <option value="STOP">STOP</option>
                    <option value="MAINTENANCE">MAINTENANCE</option>
                    <option value="TROUBLE">TROUBLE</option>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Packer 1</label>
                <input type="text" name="packer1_note" placeholder="Contoh: Normal">
            </div>

            <div class="form-group">
                <label>Status Packer 2</label>
                <select name="packer2_status">
                    <option value="RUN">RUN</option>
                    <option value="STOP">STOP</option>
                    <option value="MAINTENANCE">MAINTENANCE</option>
                    <option value="TROUBLE">TROUBLE</option>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Packer 2</label>
                <input type="text" name="packer2_note" placeholder="Contoh: Siap operasi">
            </div>

            <div class="form-group">
                <label>Produksi Packer</label>
                <input type="number" step="0.01" name="production_packer" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Truck Area Packer</label>
                <input type="number" name="truck_packer_area" placeholder="0">
            </div>

            <div class="form-group">
                <label>Truck Area Emplacement</label>
                <input type="number" name="truck_emplacement_area" placeholder="0">
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
                    <input type="number" step="0.01" name="bags[{{ $bag }}]" placeholder="0">
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="bi bi-save"></i> Simpan Laporan
            </button>
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</form>
@endsection
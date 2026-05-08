@extends('layouts.main')

@section('title', 'Input Laporan Harian')

@section('content')
    <div class="page-card">
        <h2 class="page-card-title">Laporan Nuansa Harian</h2>
        <p class="page-card-subtitle">
            Input data operasional harian GP Dumai.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin:10px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li style="margin-bottom:4px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reports.store') }}" method="POST">
        @csrf

        {{-- INFORMASI LAPORAN --}}
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
                    <input type="date" name="report_date" value="{{ old('report_date') }}" required>
                </div>
            </div>
        </div>

        {{-- CEMENT MILL DUMAI --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
                <span>1. Cement Mill Dumai</span>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Operational Cement Mill</label>
                    <select name="cement_mill_status" required>
                        @foreach (['RUN', 'STOP', 'MAINTENANCE', 'TROUBLE'] as $status)
                            <option value="{{ $status }}" @selected(old('cement_mill_status') == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Cement Mill</label>
                    <input type="text" name="cement_mill_note" value="{{ old('cement_mill_note') }}"
                        placeholder="Contoh: Operasional lancar">
                </div>

                <div class="form-group">
                    <label>Feed</label>
                    <input type="number" step="0.01" min="0" name="feed" value="{{ old('feed') }}"
                        placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Blaine</label>
                    <input type="number" step="0.01" min="0" name="blaine" value="{{ old('blaine') }}"
                        placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Sieving</label>
                    <input type="number" step="0.01" min="0" name="sieving" value="{{ old('sieving') }}"
                        placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Produksi 00.00 s/d 07.00</label>
                    <input type="number" step="0.01" min="0" name="production_cm"
                        value="{{ old('production_cm') }}" placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Running Hours</label>
                    <input type="number" step="0.01" min="0" name="running_hours"
                        value="{{ old('running_hours') }}" placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Clinker Factor</label>
                    <input type="number" step="0.01" min="0" name="clinker_factor"
                        value="{{ old('clinker_factor') }}" placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Silo Semen</label>
                    <input type="number" step="0.01" min="0" name="silo_semen" value="{{ old('silo_semen') }}"
                        placeholder="0.00">
                </div>
            </div>
        </div>

        {{-- STOCK MATERIAL --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <span>2. Stock Material</span>
            </div>

            <div class="form-grid">
                @foreach (['Semen', 'Klinker', 'Gypsum', 'Gypsum Natural', 'Gypsum Purified', 'Pozzolan', 'Flyash Wet', 'Flyash Dry', 'Limestone', 'Solar'] as $material)
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="number" step="0.01" min="0" name="stocks[{{ $material }}]"
                            value="{{ old('stocks.' . $material) }}" placeholder="0.00">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PENERIMAAN MATERIAL --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
                <span>3. Penerimaan Material 00.00 s/d 24.00</span>
            </div>

            <div class="form-grid">
                @foreach (['Semen', 'Klinker', 'Limestone', 'Gypsum', 'Pozzolan', 'Fly Ash Dry SDS', 'Fly Ash Dry ESM', 'Fly Ash Dry IK', 'Fly Ash Dry SDO', 'Fly Ash Wet Tenayan', 'Fly Ash Wet RAPP'] as $material)
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="number" step="0.01" min="0" name="receipts[{{ $material }}]"
                            value="{{ old('receipts.' . $material) }}" placeholder="0.00">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PEMAKAIAN MATERIAL --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <span>4. Pemakaian Material 00.00 s/d 24.00</span>
            </div>

            <div class="form-grid">
                @foreach (['Klinker', 'Gypsum Natural', 'Gypsum Purified', 'Pozzolan', 'Wet Fly Ash', 'Dry Fly Ash', 'Limestone', 'Solar', 'Gas'] as $material)
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="number" step="0.01" min="0" name="usages[{{ $material }}]"
                            value="{{ old('usages.' . $material) }}" placeholder="0.00">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PACKER DUMAI --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <span>5. Packer Dumai</span>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Peralatan</label>
                    <input type="text" value="Packer 1" readonly>
                </div>

                <div class="form-group">
                    <label>Kondisi Packer 1</label>
                    <select name="packer1_status" required>
                        @foreach (['READY', 'MAINTENANCE', 'STOP', 'TROUBLE'] as $status)
                            <option value="{{ $status }}" @selected(old('packer1_status') == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Keterangan Packer 1</label>
                    <input type="text" name="packer1_note" value="{{ old('packer1_note') }}"
                        placeholder="Contoh: Operasional lancar">
                </div>

                <div class="form-group">
                    <label>Peralatan</label>
                    <input type="text" value="Packer 2" readonly>
                </div>

                <div class="form-group">
                    <label>Kondisi Packer 2</label>
                    <select name="packer2_status" required>
                        @foreach (['READY', 'MAINTENANCE', 'STOP', 'TROUBLE'] as $status)
                            <option value="{{ $status }}" @selected(old('packer2_status') == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Keterangan Packer 2</label>
                    <input type="text" name="packer2_note" value="{{ old('packer2_note') }}"
                        placeholder="Contoh: Operasional lancar">
                </div>
            </div>
        </div>

        {{-- ANTRIAN TRUK --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <span>6. Antrian Truk</span>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Area Packer</label>
                    <input type="number" min="0" name="truck_packer_area"
                        value="{{ old('truck_packer_area') }}" placeholder="0">
                </div>

                <div class="form-group">
                    <label>Area Emplacement</label>
                    <input type="number" min="0" name="truck_emplacement_area"
                        value="{{ old('truck_emplacement_area') }}" placeholder="0">
                </div>
            </div>
        </div>

        {{-- PRODUKSI PACKER --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <span>7. Produksi Packer</span>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Total Produksi Packer</label>
                    <input type="number" step="0.01" min="0" name="production_packer"
                        value="{{ old('production_packer') }}" placeholder="0.00">
                </div>
            </div>
        </div>

        {{-- STOCK KANTONG --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-archive"></i>
                </div>
                <span>Stock Kantong</span>
            </div>

            <div class="form-grid">
                @foreach (['BB 50 KG SP', 'BB 40 KG SP', 'Dinamik 50 KG', 'Dinamik 40 KG', 'Merdeka 50 KG', 'Merdeka 50 KG'] as $bag)
                    <div class="form-group">
                        <label>{{ $bag }}</label>
                        <input type="number" step="1" min="0" name="bags[{{ $bag }}]"
                            value="{{ old('bags.' . $bag) }}" placeholder="0">
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

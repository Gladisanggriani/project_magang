@extends('layouts.main')

@section('title', 'Input Laporan Harian')

@section('content')
    <div class="page-card">
        <h2 class="page-card-title">Input Laporan Nuansa Harian</h2>
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
                    <label>Feed</label>
                    <input type="text" name="feed" readonly inputmode="decimal" value="{{ old('feed') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Blaine</label>
                    <input type="text" inputmode="decimal" name="blaine" value="{{ old('blaine') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Sieving</label>
                    <input type="text" inputmode="decimal" name="sieving" value="{{ old('sieving') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Produksi Semen</label>
                    <input type="text" inputmode="decimal" name="production_cm" value="{{ old('production_cm') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Jam Start</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}">
                </div>

                <div class="form-group">
                    <label>Jam Stop</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}">
                </div>

                <div class="form-group">
                    <label>Clinker Factor</label>
                    <input type="text" inputmode="decimal" name="clinker_factor" value="{{ old('clinker_factor') }}"
                        placeholder="Contoh: 70,50">
                </div>

                <div class="form-group">
                    <label>Produksi dari Kapal</label>
                    <input type="text" inputmode="decimal" name="production_ship" value="{{ old('production_ship') }}"
                        placeholder="Contoh: 2.520,78">
                    @error('production_ship')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Stock Awal Silo</label>
                    <input type="text" inputmode="decimal" name="stock_awal_silo" value="{{ old('stock_awal_silo') }}"
                        placeholder="Contoh: 1.250,00">
                </div>

                <div class="form-group">
                    <label>Silo Semen</label>
                    <input type="text" inputmode="decimal" name="silo_semen" readonly value="{{ old('silo_semen') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Keterangan Cement Mill</label>
                    <textarea name="cement_mill_note" rows="4" placeholder="Contoh: Operasional lancar"
                        style="width: 100%; min-height: 120px; resize: vertical; line-height: 1.6;">{{ old('cement_mill_note') }}</textarea>
                </div>
            </div>
        </div>

        {{-- STOCK MATERIAL OTOMATIS --}}
        <div class="input-note">
            Stock material dihitung otomatis dari stock akhir sebelumnya + penerimaan material - pemakaian material.
        </div>

        {{-- PENERIMAAN MATERIAL --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
                <span>2. Penerimaan Material 00.00 s/d 24.00</span>
            </div>

            <div class="form-grid">
                @foreach (['Semen', 'Klinker', 'Limestone', 'Gypsum', 'Pozzolan', 'Fly Ash', 'Fly Ash Dry SDS', 'Fly Ash Dry ESM', 'Fly Ash Dry IK', 'Fly Ash Dry SDO', 'Fly Ash Wet Tenayan', 'Fly Ash Wet RAPP'] as $material)
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="text" inputmode="decimal" name="receipts[{{ $material }}]"
                            value="{{ old('receipts.' . $material) }}" placeholder="0,00">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- INTRANSIT MATERIAL --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <span>3. Intransit Material</span>
            </div>

            <div class="form-grid">
                @foreach (['Semen', 'Klinker', 'Limestone', 'Gypsum', 'Pozzolan', 'Fly Ash', 'Fly Ash Dry SDS', 'Fly Ash Dry ESM', 'Fly Ash Dry IK', 'Fly Ash Dry SDO', 'Fly Ash Wet Tenayan', 'Fly Ash Wet RAPP'] as $material)
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="text" inputmode="decimal" name="intransits[{{ $material }}]"
                            value="{{ old('intransits.' . $material) }}" placeholder="0,00">
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
                @foreach (['Klinker', 'Gypsum Natural', 'Gypsum Purified', 'Dry Fly Ash', 'Pozzolan', 'Fly Ash', 'Wet Fly Ash', 'Limestone', 'Solar', 'Gas'] as $material)
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="text" inputmode="decimal" name="usages[{{ $material }}]"
                            value="{{ old('usages.' . $material) }}" placeholder="0,00">
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
                    <input type="number" min="0" step="1" name="truck_packer_area"
                        value="{{ old('truck_packer_area') }}" placeholder="0">
                </div>

                <div class="form-group">
                    <label>Area Emplacement</label>
                    <input type="number" min="0" step="1" name="truck_emplacement_area"
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
                    <input type="text" inputmode="decimal" name="production_packer"
                        value="{{ old('production_packer') }}" placeholder="Contoh: 2.520,78">
                </div>
            </div>
        </div>

        {{-- STOCK KANTONG --}}
        <div class="form-section">
            <div class="form-section-title">
                <div class="form-section-icon">
                    <i class="bi bi-archive"></i>
                </div>
                <span>8. Stock Kantong</span>
            </div>

            <div class="form-grid">
                @foreach (['BB 50 KG SP', 'BB 40 KG SP', 'Dinamik 50 KG', 'Dinamik 40 KG', 'Merdeka 50 KG', 'Merdeka 40 KG'] as $bag)
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const productionCm = document.querySelector('[name="production_cm"]');
                const productionPacker = document.querySelector('[name="production_packer"]');
                const stockAwalSilo = document.querySelector('[name="stock_awal_silo"]');

                const startTime = document.querySelector('[name="start_time"]');
                const endTime = document.querySelector('[name="end_time"]');

                const feedInput = document.querySelector('[name="feed"]');
                const siloInput = document.querySelector('[name="silo_semen"]');

                function parseNumber(value) {

                    if (!value) return 0;

                    value = value.replace(/\./g, '');
                    value = value.replace(',', '.');

                    return parseFloat(value) || 0;
                }

                function calculate() {

                    const cm = parseNumber(productionCm.value);
                    const packer = parseNumber(productionPacker.value);
                    const stockAwal = parseNumber(stockAwalSilo.value);

                    let runningHours = 0;

                    if (startTime.value && endTime.value) {

                        const start = new Date('2000-01-01 ' + startTime.value);
                        const end = new Date('2000-01-01 ' + endTime.value);

                        let diff = (end - start) / 1000 / 60 / 60;

                        if (diff < 0) {
                            diff += 24;
                        }

                        runningHours = diff;
                    }

                    let feed = 0;

                    if (runningHours > 0) {
                        feed = cm / runningHours;
                    }

                    const silo = cm + stockAwal - packer;

                    feedInput.value = feed.toFixed(2);
                    siloInput.value = silo.toFixed(2);
                }

                productionCm.addEventListener('input', calculate);
                productionPacker.addEventListener('input', calculate);
                stockAwalSilo.addEventListener('input', calculate);

                startTime.addEventListener('change', calculate);
                endTime.addEventListener('change', calculate);

            });
        </script>
    </form>
@endsection

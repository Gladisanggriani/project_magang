@extends('layouts.main')

@section('title', 'Edit Laporan Harian')

@section('content')
    @php
        $stockValue = fn($name) => optional($report->materialStocks->firstWhere('material_name', $name))->quantity;
        $receiptValue = fn($name) => optional($report->materialReceipts->firstWhere('material_name', $name))->quantity;
        $usageValue = fn($name) => optional($report->materialUsages->firstWhere('material_name', $name))->quantity;
        $bagValue = fn($name) => optional($report->bagStocks->firstWhere('bag_type', $name))->quantity;
        $intransitValue = fn($name) => optional($report->materialIntransits->firstWhere('material_name', $name))->quantity;
    @endphp

    <div class="page-card">
        <h2 class="page-card-title">Edit Laporan Nuansa Harian</h2>
        <p class="page-card-subtitle">
            Perbarui data operasional harian GP Dumai. Setelah disimpan, data pada dashboard dan database akan ikut berubah.
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

    <form action="{{ route('reports.update', $report->id) }}" method="POST">
        @csrf
        @method('PUT')

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
                    <input type="date" name="report_date" value="{{ old('report_date', $report->report_date) }}" required>
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
                            <option value="{{ $status }}" @selected(old('cement_mill_status', $report->cement_mill_status) == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Feed</label>
                    <input type="text" name="feed" readonly inputmode="decimal"
                        value="{{ old('feed', $report->feed ? number_format((float)$report->feed, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 2.520,78" style="background-color: #f3f4f6;">
                </div>

                <div class="form-group">
                    <label>Blaine</label>
                    <input type="text" inputmode="decimal" name="blaine"
                        value="{{ old('blaine', $report->blaine ? number_format((float)$report->blaine, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Sieving</label>
                    <input type="text" inputmode="decimal" name="sieving"
                        value="{{ old('sieving', $report->sieving ? number_format((float)$report->sieving, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Produksi Semen</label>
                    <input type="text" inputmode="decimal" name="production_cm"
                        value="{{ old('production_cm', $report->production_cm ? number_format((float)$report->production_cm, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 2.520,78">
                </div>

                <div class="form-group">
                    <label>Jam Start</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $report->start_time ? substr($report->start_time, 0, 5) : '') }}">
                </div>

                <div class="form-group">
                    <label>Jam Stop</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $report->end_time ? substr($report->end_time, 0, 5) : '') }}">
                </div>

                <div class="form-group">
                    <label>Clinker Factor</label>
                    <input type="text" inputmode="decimal" name="clinker_factor"
                        value="{{ old('clinker_factor', $report->clinker_factor ? number_format((float)$report->clinker_factor, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 70,50">
                </div>

                <div class="form-group">
                    <label>Produksi dari Kapal</label>
                    <input type="text" inputmode="decimal" name="production_ship"
                        value="{{ old('production_ship', $report->production_ship ? number_format((float)$report->production_ship, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 2.520,78">
                    @error('production_ship')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Stock Awal Silo</label>
                    <input type="text" inputmode="decimal" name="stock_awal_silo"
                        value="{{ old('stock_awal_silo', $report->stock_awal_silo ? number_format((float)$report->stock_awal_silo, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 1.250,00">
                </div>

                <div class="form-group">
                    <label>Silo Semen</label>
                    <input type="text" inputmode="decimal" name="silo_semen" readonly
                        value="{{ old('silo_semen', $report->silo_semen ? number_format((float)$report->silo_semen, 2, ',', '.') : '') }}"
                        placeholder="Contoh: 2.520,78" style="background-color: #f3f4f6;">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Keterangan Cement Mill</label>
                    <textarea name="cement_mill_note" rows="4" placeholder="Contoh: Operasional lancar"
                        style="width: 100%; min-height: 120px; resize: vertical; line-height: 1.6;">{{ old('cement_mill_note', $report->cement_mill_note) }}</textarea>
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
                    @php
                        $value = $receiptValue($material);
                    @endphp
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="text" inputmode="decimal" name="receipts[{{ $material }}]"
                            value="{{ old('receipts.' . $material, $value !== null ? number_format((float)$value, 2, ',', '.') : '') }}" placeholder="0,00">
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
                    @php
                        $value = $intransitValue($material);
                    @endphp
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="text" inputmode="decimal" name="intransits[{{ $material }}]"
                            value="{{ old('intransits.' . $material, $value !== null ? number_format((float)$value, 2, ',', '.') : '') }}" placeholder="0,00">
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
                    @php
                        $value = $usageValue($material);
                    @endphp
                    <div class="form-group">
                        <label>{{ $material }}</label>
                        <input type="text" inputmode="decimal" name="usages[{{ $material }}]"
                            value="{{ old('usages.' . $material, $value !== null ? number_format((float)$value, 2, ',', '.') : '') }}" placeholder="0,00">
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
                    <input type="text" value="Packer 1" readonly style="background-color: #f3f4f6;">
                </div>

                <div class="form-group">
                    <label>Kondisi Packer 1</label>
                    <select name="packer1_status" required>
                        @foreach (['READY', 'MAINTENANCE', 'STOP', 'TROUBLE'] as $status)
                            <option value="{{ $status }}" @selected(old('packer1_status', $report->packer1_status) == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Keterangan Packer 1</label>
                    <input type="text" name="packer1_note" value="{{ old('packer1_note', $report->packer1_note) }}"
                        placeholder="Contoh: Operasional lancar">
                </div>

                <div class="form-group">
                    <label>Peralatan</label>
                    <input type="text" value="Packer 2" readonly style="background-color: #f3f4f6;">
                </div>

                <div class="form-group">
                    <label>Kondisi Packer 2</label>
                    <select name="packer2_status" required>
                        @foreach (['READY', 'MAINTENANCE', 'STOP', 'TROUBLE'] as $status)
                            <option value="{{ $status }}" @selected(old('packer2_status', $report->packer2_status) == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Keterangan Packer 2</label>
                    <input type="text" name="packer2_note" value="{{ old('packer2_note', $report->packer2_note) }}"
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
                        value="{{ old('truck_packer_area', $report->truck_packer_area) }}" placeholder="0">
                </div>

                <div class="form-group">
                    <label>Area Emplacement</label>
                    <input type="number" min="0" step="1" name="truck_emplacement_area"
                        value="{{ old('truck_emplacement_area', $report->truck_emplacement_area) }}" placeholder="0">
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
                    <label>Produksi Packer 1</label>
                    <input type="text" inputmode="decimal" name="production_packer1"
                        value="{{ old('production_packer1', $report->production_packer1 ? number_format((float)$report->production_packer1, 2, ',', '.') : '') }}" placeholder="Contoh: 1.000,00">
                </div>

                <div class="form-group">
                    <label>Produksi Packer 2</label>
                    <input type="text" inputmode="decimal" name="production_packer2"
                        value="{{ old('production_packer2', $report->production_packer2 ? number_format((float)$report->production_packer2, 2, ',', '.') : '') }}" placeholder="Contoh: 1.520,78">
                </div>

                <div class="form-group">
                    <label>Total Produksi Packer</label>
                    <input type="text" inputmode="decimal" name="production_packer" readonly
                        value="{{ old('production_packer', $report->production_packer ? number_format((float)$report->production_packer, 2, ',', '.') : '') }}" placeholder="Dihitung otomatis" style="background-color: #f3f4f6;">
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
                    @php
                        $bagKey = str_replace(' ', '_', $bag);
                        $value = old('bags.' . $bagKey, isset($bagValue) ? $bagValue($bag) : null);
                    @endphp
                    <div class="form-group">
                        <label>{{ $bag }}</label>
                        <input type="text" inputmode="decimal" name="bags[{{ $bag }}]"
                            value="{{ $value !== null ? number_format((float)$value, 2, ',', '.') : '' }}"
                            placeholder="Contoh: 1.000,00">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const productionCm = document.querySelector('[name="production_cm"]');
            const stockAwalSilo = document.querySelector('[name="stock_awal_silo"]');

            const productionPacker1 = document.querySelector('[name="production_packer1"]');
            const productionPacker2 = document.querySelector('[name="production_packer2"]');
            const productionPackerTotal = document.querySelector('[name="production_packer"]');

            const startTime = document.querySelector('[name="start_time"]');
            const endTime = document.querySelector('[name="end_time"]');

            const feedInput = document.querySelector('[name="feed"]');
            const siloInput = document.querySelector('[name="silo_semen"]');

            function parseNumber(value) {
                if (!value) return 0;
                value = value.replace(/\./g, ''); // Hapus titik (ribuan)
                value = value.replace(',', '.');  // Ganti koma dengan titik (desimal)
                return parseFloat(value) || 0;
            }

            function calculate() {
                const cm = parseNumber(productionCm.value);
                const packer1 = parseNumber(productionPacker1.value);
                const packer2 = parseNumber(productionPacker2.value);
                const stockAwal = parseNumber(stockAwalSilo.value);

                // 1. Menghitung Total Produksi Packer
                const totalPacker = packer1 + packer2;
                productionPackerTotal.value = totalPacker > 0 ? totalPacker.toFixed(2).replace('.', ',') : '';

                // 2. Menghitung Running Hours & Feed
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

                // 3. Menghitung Silo Semen
                const silo = cm + stockAwal - totalPacker;

                feedInput.value = feed.toFixed(2).replace('.', ',');
                siloInput.value = silo.toFixed(2).replace('.', ',');
            }

            // Jalankan fungsi saat ada input
            productionCm.addEventListener('input', calculate);
            productionPacker1.addEventListener('input', calculate);
            productionPacker2.addEventListener('input', calculate);
            stockAwalSilo.addEventListener('input', calculate);

            startTime.addEventListener('change', calculate);
            endTime.addEventListener('change', calculate);

            // Inisiasi kalkulasi saat page dimuat (untuk file edit agar value sinkron)
            calculate();
        });
    </script>
@endsection

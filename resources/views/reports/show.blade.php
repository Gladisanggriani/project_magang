@extends('layouts.main')

@section('title', 'Detail Laporan Harian')

@section('content')
    @php
        $totalTruck = ($report->truck_packer_area ?? 0) + ($report->truck_emplacement_area ?? 0);

        $statusClass = function ($status) {
            return match (strtoupper($status ?? '')) {
                'RUN', 'READY' => 'report-status success',
                'STOP', 'TROUBLE' => 'report-status danger',
                'MAINTENANCE' => 'report-status warning',
                default => 'report-status neutral',
            };
        };
    @endphp

    <div class="report-page-action">
        <a href="{{ route('reports.index') }}" class="btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <a href="{{ route('reports.export-excel', $report->id) }}" class="btn-secondary">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>

        @if (auth()->user()->hasRole(['admin', 'operator']))
            <a href="{{ route('reports.edit', $report->id) }}" class="btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Laporan
            </a>
        @endif
    </div>

    <div class="report-document">
        <div class="report-header">
            <h1>LAPORAN NUANSA HARIAN</h1>
            <h2>PT Semen Padang Unit Pabrik Dumai</h2>
            <p>
                Tanggal Laporan:
                <strong>{{ \Carbon\Carbon::parse($report->report_date)->format('d F Y') }}</strong>
            </p>
        </div>

        <div class="report-info-box">
            <div>
                <span>Operational Cement Mill</span>
                <strong>{{ $report->cement_mill_status ?: '-' }}</strong>
            </div>
            <div>
                <span>Produksi Semen</span>
                <strong>{{ number_format($report->production_cm, 2, ',', '.') }} Ton</strong>
            </div>

            <div>
                <span>Produksi dari Kapal</span>
                <strong>{{ number_format($report->production_ship ?? 0, 2, ',', '.') }} Ton</strong>
            </div>
            <div>
                <span>Produksi Packer</span>
                <strong>{{ number_format($report->production_packer, 2, ',', '.') }} Ton</strong>
            </div>
            <div>
                <span>Total Antrian Truk</span>
                <strong>{{ $totalTruck }} Truck</strong>
            </div>
        </div>

        <div class="report-section">
            <div class="report-section-title">1. Cement Mill Dumai</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Parameter</th>
                        <th style="width: 30%;">Nilai</th>
                        <th style="width: 30%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Operational Cement Mill</td>
                        <td>
                            <span class="{{ $statusClass($report->cement_mill_status) }}">
                                {{ $report->cement_mill_status ?: '-' }}
                            </span>
                        </td>
                        <td>{{ $report->cement_mill_note ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>Feed</td>
                        <td>{{ number_format($report->feed, 2, ',', '.') }}</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Blaine</td>
                        <td>{{ number_format($report->blaine, 2, ',', '.') }}</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Sieving</td>
                        <td>{{ number_format($report->sieving, 2, ',', '.') }}</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Produksi 00.00 s/d 07.00</td>
                        <td>{{ number_format($report->production_cm, 2, ',', '.') }} Ton</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Running Hours</td>
                        <td>{{ number_format($report->running_hours, 2, ',', '.') }} Jam</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Clinker Factor</td>
                        <td>{{ number_format($report->clinker_factor, 2, ',', '.') }}</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Produksi Semen</td>
                        <td>{{ number_format($report->production_cm, 2, ',', '.') }} Ton</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Produksi dari Kapal</td>
                        <td>{{ number_format($report->production_ship ?? 0, 2, ',', '.') }} Ton</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">2. Stock Material</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Material</th>
                        <th style="width:180px;">Qty</th>
                        <th style="width:120px;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report->materialStocks as $index => $stock)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $stock->material_name }}</td>
                            <td class="text-right">{{ number_format($stock->quantity, 2, ',', '.') }}</td>
                            <td>{{ $stock->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data stock material.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">3. Penerimaan Material 00.00 s/d 24.00</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Material</th>
                        <th style="width:180px;">Qty</th>
                        <th style="width:120px;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report->materialReceipts as $index => $receipt)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $receipt->material_name }}</td>
                            <td class="text-right">{{ number_format($receipt->quantity, 2, ',', '.') }}</td>
                            <td>{{ $receipt->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data penerimaan material.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">4. Intransit Material</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Material</th>
                        <th style="width:180px;">Qty</th>
                        <th style="width:120px;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report->materialIntransits as $index => $intransit)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $intransit->material_name }}</td>
                            <td class="text-right">{{ number_format($intransit->quantity, 2, ',', '.') }}</td>
                            <td>{{ $intransit->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data intransit material.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">5. Pemakaian Material 00.00 s/d 24.00</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Material</th>
                        <th style="width:180px;">Qty</th>
                        <th style="width:120px;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report->materialUsages as $index => $usage)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $usage->material_name }}</td>
                            <td class="text-right">{{ number_format($usage->quantity, 2, ',', '.') }}</td>
                            <td>{{ $usage->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data pemakaian material.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">6. Packer Dumai</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Peralatan</th>
                        <th>Kondisi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Packer 1</td>
                        <td>
                            <span class="{{ $statusClass($report->packer1_status) }}">
                                {{ $report->packer1_status ?: '-' }}
                            </span>
                        </td>
                        <td>{{ $report->packer1_note ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>Packer 2</td>
                        <td>
                            <span class="{{ $statusClass($report->packer2_status) }}">
                                {{ $report->packer2_status ?: '-' }}
                            </span>
                        </td>
                        <td>{{ $report->packer2_note ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">7. Antrian Truk</div>

            <table class="report-table antrian-table">
                <tbody>
                    <tr class="antrian-label-row">
                        <th>Area Packer</th>
                        <th>Area Emplacement</th>
                    </tr>
                    <tr>
                        <td>{{ $report->truck_packer_area }} Truck</td>
                        <td>{{ $report->truck_emplacement_area }} Truck</td>
                    </tr>
                    <tr class="total-antrian-row">
                        <th>Total Antrian</th>
                        <td><strong>{{ $totalTruck }} Truck</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">8. Produksi Packer</div>

            <table class="report-table">
                <tbody>
                    <tr>
                        <th>Total Produksi Packer</th>
                        <td>{{ number_format($report->production_packer, 2, ',', '.') }} Ton</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <div class="report-section-title">9. Stock Kantong</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Jenis Kantong</th>
                        <th style="width:180px;">Qty</th>
                        <th style="width:120px;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report->bagStocks as $index => $bag)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $bag->bag_type }}</td>
                            <td class="text-right">{{ number_format($bag->quantity, 0, ',', '.') }}</td>
                            <td>{{ $bag->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data stock kantong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.main')

@section('title', 'Preview Rekap Laporan')

@section('content')
    @php
        $totalProductionCm = 0;
        $totalProductionShip = 0;
        $totalProductionPacker = 0;
        $totalSiloSemen = 0;
        $totalClosingStock = 0;
        $totalTruckAll = 0;
    @endphp

    <div class="report-page-action">
        <a href="{{ route('reports.index', request()->query()) }}" class="btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <a href="{{ route('reports.export-monthly', request()->query()) }}" class="btn-primary">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
    </div>

    <div class="report-document">
        <div class="report-header">
            <h1>REKAP LAPORAN BULANAN</h1>
            <h2>PT Semen Padang Unit Pabrik Dumai</h2>
            <p>
                Filter:
                <strong>{{ $filterTitle }}</strong>
            </p>
        </div>

        <div class="report-section">
            <div class="report-section-title">Informasi Rekap Laporan</div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Produksi Semen</th>
                        <th>Produksi dari Kapal</th>
                        <th>Produksi Packer</th>
                        <th>Silo Semen</th>
                        <th>Closing Stock</th>
                        <th>Status Mill</th>
                        <th>Total Antrian</th>
                    </tr>
                </thead>
                <tbody>
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
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') }}
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($report->production_cm ?? 0, 2, ',', '.') }} Ton
                            </td>
                            <td class="text-right">
                                {{ number_format($report->production_ship ?? 0, 2, ',', '.') }} Ton
                            </td>
                            <td class="text-right">
                                {{ number_format($report->production_packer ?? 0, 2, ',', '.') }} Ton
                            </td>
                            <td class="text-right">
                                {{ number_format($report->silo_semen ?? 0, 2, ',', '.') }} Ton
                            </td>
                            <td class="text-right">
                                {{ number_format($closingStock, 2, ',', '.') }} Ton
                            </td>
                            <td class="text-center">
                                {{ $report->cement_mill_status ?: '-' }}
                            </td>
                            <td class="text-center">
                                {{ $totalTruck }} Truck
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                Tidak ada data laporan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse

                    @if ($reports->count() > 0)
                        <tr class="total-antrian-row">
                            <th colspan="3" class="text-center">TOTAL</th>
                            <th class="text-right">{{ number_format($totalProductionCm, 2, ',', '.') }} Ton</th>
                            <th class="text-right">{{ number_format($totalProductionShip, 2, ',', '.') }} Ton</th>
                            <th class="text-right">{{ number_format($totalProductionPacker, 2, ',', '.') }} Ton</th>
                            <th class="text-right">{{ number_format($totalSiloSemen, 2, ',', '.') }} Ton</th>
                            <th class="text-right">{{ number_format($totalClosingStock, 2, ',', '.') }} Ton</th>
                            <th class="text-center">-</th>
                            <th class="text-center">{{ $totalTruckAll }} Truck</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
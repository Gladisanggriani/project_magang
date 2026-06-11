@extends('layouts.main')

@section('title', 'Riwayat Laporan')

@section('content')
    @php
        $hasFilter =
            request()->filled('report_date') ||
            request()->filled('month') ||
            request()->filled('year') ||
            request()->filled('weekday') ||
            request()->filled('status');

        $monthNames = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $weekdayNames = [
            '1' => 'Senin',
            '2' => 'Selasa',
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jumat',
            '6' => 'Sabtu',
            '7' => 'Minggu',
        ];
    @endphp

    <div class="page-card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 class="page-card-title">Riwayat Laporan Harian</h2>
                <p class="page-card-subtitle">
                    Gunakan filter untuk mencari laporan berdasarkan tanggal, bulan, hari, atau status operasional.
                </p>
            </div>

            @auth
                @if (auth()->user()->hasRole(['admin', 'operator']))
                    <a href="{{ route('reports.create') }}" class="btn-primary">
                        <i class="bi bi-plus-circle"></i> Input Laporan Baru
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <div class="form-section filter-report-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-funnel"></i>
            </div>
            <span>Filter Laporan</span>
        </div>

        <form action="{{ route('reports.index') }}" method="GET">
            <div class="filter-report-grid">
                <div class="form-group mt-3">
                    <label>Tanggal Laporan</label>
                    <input type="date" name="report_date" value="{{ request('report_date') }}">
                </div>

                <div class="form-group mt-3">
                    <label>Bulan Laporan</label>

                    <div class="filter-month-year">
                        <select name="month">
                            <option value="">Pilih Bulan</option>
                            @foreach ([
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ] as $monthValue => $monthName)
                                <option value="{{ $monthValue }}" @selected(request('month') == $monthValue)>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>

                        <select name="year">
                            <option value="">Tahun</option>
                            @for ($year = now()->year + 1; $year >= now()->year - 5; $year--)
                                <option value="{{ $year }}" @selected(request('year') == $year)>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label>Hari</label>
                    <select name="weekday">
                        <option value="">Semua Hari</option>
                        <option value="1" @selected(request('weekday') == '1')>Senin</option>
                        <option value="2" @selected(request('weekday') == '2')>Selasa</option>
                        <option value="3" @selected(request('weekday') == '3')>Rabu</option>
                        <option value="4" @selected(request('weekday') == '4')>Kamis</option>
                        <option value="5" @selected(request('weekday') == '5')>Jumat</option>
                        <option value="6" @selected(request('weekday') == '6')>Sabtu</option>
                        <option value="7" @selected(request('weekday') == '7')>Minggu</option>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label>Status Mesin / Packer</label>
                    <select name="status">
                        <option value="">Semua Status</option>
                        @foreach (['RUN', 'STOP', 'MAINTENANCE', 'TROUBLE'] as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-actions filter-actions mt-4">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-search"></i> Cari Laporan
                </button>

                <a href="{{ route('reports.index') }}" class="btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset Filter
                </a>

                <a href="{{ route('reports.preview-monthly', request()->query()) }}" class="btn-secondary">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>
        </form>
    </div>

    @if ($hasFilter)
        <div class="{{ $reports->total() > 0 ? 'alert-success' : 'alert-danger' }}">
            @if ($reports->total() > 0)
                <strong>Data ditemukan.</strong>
                Sistem menemukan {{ $reports->total() }} laporan

                @if (request('report_date'))
                    untuk tanggal {{ \Carbon\Carbon::parse(request('report_date'))->format('d-m-Y') }}
                @endif

                @if (request('month'))
                    bulan {{ $monthNames[request('month')] ?? '-' }}
                @endif

                @if (request('year'))
                    tahun {{ request('year') }}
                @endif

                @if (request('weekday'))
                    pada hari {{ $weekdayNames[request('weekday')] ?? '-' }}
                @endif

                @if (request('status'))
                    dengan status {{ request('status') }}
                @endif
                .
            @else
                <strong>Data tidak ditemukan.</strong>
                Tidak ada laporan yang sesuai dengan filter pencarian.
            @endif
        </div>
    @endif

    <div class="page-card">
        <div
            style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div>
                <h3 class="panel-title">Data Laporan</h3>
                <p class="page-card-subtitle">
                    @if ($hasFilter)
                        Total hasil filter: {{ $reports->total() }} laporan
                    @else
                        Total seluruh laporan: {{ $reports->total() }} laporan
                    @endif
                </p>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produksi Semen</th>
                        <th>Produksi Kapal</th>
                        <th>Total Produksi Packer</th>
                        <th>Operational Mill</th>
                        <th>Kondisi Packer 1</th>
                        <th>Kondisi Packer 2</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') }}</td>
                            <td>{{ number_format($report->production_cm ?? 0, 2, ',', '.') }} Ton</td>
                            <td>{{ number_format($report->production_ship ?? 0, 2, ',', '.') }} Ton</td>
                            <td>{{ number_format($report->production_packer ?? 0, 2, ',', '.') }} Ton</td>
                            <td>{{ $report->cement_mill_status }}</td>
                            <td>{{ $report->packer1_status }}</td>
                            <td>{{ $report->packer2_status }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('reports.show', $report->id) }}" class="btn-action-table btn-view">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>

                                    <a href="{{ route('reports.export-excel', $report->id) }}"
                                        class="btn-action-table btn-excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                        <span>Excel</span>
                                    </a>

                                    @auth
                                        @if (auth()->user()->hasRole(['admin', 'operator']))
                                            <a href="{{ route('reports.edit', $report->id) }}"
                                                class="btn-action-table btn-edit">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>Edit</span>
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasRole('admin'))
                                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST"
                                                class="delete-form"
                                                onsubmit="return confirm('Yakin ingin menghapus laporan ini? Data yang dihapus tidak bisa dikembalikan.');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn-action-table btn-delete">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                @if ($hasFilter)
                                    Tidak ada laporan yang sesuai dengan filter pencarian.
                                @else
                                    Belum ada laporan yang tersimpan.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap" style="margin-top:18px;">
            {{ $reports->links() }}
        </div>
    </div>
@endsection

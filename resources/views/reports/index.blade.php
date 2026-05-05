@extends('layouts.main')

@section('title', 'Riwayat Laporan')

@section('content')
    <div class="page-card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 class="page-card-title">Riwayat Laporan Harian</h2>
                <p class="page-card-subtitle">
                    Gunakan filter untuk mencari laporan berdasarkan tanggal laporan atau status operasional. </p>
            </div>

            @if (auth()->user()->hasRole(['admin', 'operator']))
                <a href="{{ route('reports.create') }}" class="btn-primary">
                    <i class="bi bi-plus-circle"></i> Input Laporan Baru
                </a>
            @endif
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-funnel"></i>
            </div>
            <span>Filter Laporan</span>
        </div>

        <form action="{{ route('reports.index') }}" method="GET">
            <div class="form-grid">
                <div class="form-group">
                    <label>Tanggal Laporan</label>
                    <input type="date" name="report_date" value="{{ request('report_date') }}">
                </div>

                <div class="form-group">
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

            <div class="form-actions" style="margin-top:18px;">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-search"></i> Cari Laporan
                </button>

                <a href="{{ route('reports.index') }}" class="btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>
    @if (request()->filled('report_date') || request()->filled('status'))
        <div class="{{ $reports->total() > 0 ? 'alert-success' : 'alert-danger' }}">
            @if ($reports->total() > 0)
                <strong>Data ditemukan.</strong>
                Sistem menemukan {{ $reports->total() }} laporan
                @if (request('report_date'))
                    untuk tanggal {{ \Carbon\Carbon::parse(request('report_date'))->format('d-m-Y') }}
                @endif
                @if (request('status'))
                    dengan status {{ request('status') }}
                @endif
                .
            @else
                <strong>Data tidak ditemukan.</strong>
                Tidak ada laporan
                @if (request('report_date'))
                    pada tanggal {{ \Carbon\Carbon::parse(request('report_date'))->format('d-m-Y') }}
                @endif
                @if (request('status'))
                    dengan status {{ request('status') }}
                @endif
                .
            @endif
        </div>
    @endif

    <div class="page-card">
        <div
            style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
            <div>
                <h3 class="panel-title">Data Laporan</h3>
                <p class="page-card-subtitle">
                    @if (request()->filled('report_date') || request()->filled('status'))
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
                        <th>Produksi CM 00-07</th>
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
                            <td>{{ number_format($report->production_cm, 2, ',', '.') }} Ton</td>
                            <td>{{ number_format($report->production_packer, 2, ',', '.') }} Ton</td>
                            <td>{{ $report->cement_mill_status }}</td>
                            <td>{{ $report->packer1_status }}</td>
                            <td>{{ $report->packer2_status }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('reports.show', $report->id) }}" class="btn-action-table btn-view">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>

                                    @if (auth()->user()->hasRole(['admin', 'operator']))
                                        <a href="{{ route('reports.edit', $report->id) }}"
                                            class="btn-action-table btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('reports.export-excel', $report->id) }}"
                                        class="btn-action-table btn-excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                        <span>Excel</span>
                                    </a>

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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                @if (request()->filled('report_date') || request()->filled('status'))
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

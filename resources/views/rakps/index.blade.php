@extends('layouts.main')

@section('title', 'RKAP')

@section('content')
    @php
        $canManageRakp =
            auth()->check() &&
            auth()
                ->user()
                ->hasRole(['admin', 'operator']);
    @endphp

    <div class="page-card rakp-hero">
        <div>
            <h2 class="page-card-title">
                {{ $canManageRakp ? 'Input RKAP' : 'Data RKAP' }}
            </h2>
            <p class="page-card-subtitle">
                @if ($canManageRakp)
                    Input data RKAP tahunan untuk Cement Mill dan Packer.
                @else
                    Mode viewer: data RKAP hanya dapat dilihat.
                @endif
            </p>
        </div>
    </div>

    @if ($errors->any() && $canManageRakp)
        <div class="alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin: 10px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li style="margin-bottom: 4px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('rakps.store') }}" method="POST">
        @csrf

        <div class="rkap-page">
            @if ($canManageRakp)
                <div class="rkap-header">
                    <div>
                        <span class="rkap-label">Data Manual Tahunan</span>
                        <h3>RKAP Cement Mill dan Packer</h3>
                    </div>

                    <div class="rkap-year">
                        <label>Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $year) }}" min="2000" max="2100"
                            required>
                    </div>
                </div>

                <div class="rkap-note">
                    Gunakan format angka Indonesia, contoh: 56.000,00.
                </div>
            @else
                <input type="hidden" name="year" value="{{ $year }}">
            @endif

            <div class="rkap-total-grid">
                <div class="rkap-total-card">
                    <span>Total RKAP Cement Mill</span>
                    <strong>{{ number_format($totalCementMill ?? 0, 2, ',', '.') }} Ton</strong>
                </div>

                <div class="rkap-total-card">
                    <span>Total RKAP Packer</span>
                    <strong>{{ number_format($totalPacker ?? 0, 2, ',', '.') }} Ton</strong>
                </div>

                <div class="rkap-total-card highlight">
                    <span>Total Keseluruhan</span>
                    <strong>{{ number_format($grandTotal ?? 0, 2, ',', '.') }} Ton</strong>
                </div>
                <br>
            </div>

            @if ($canManageRakp)
                <div class="rkap-group-card">
                    <div class="rkap-group-head">
                        <div>
                            <h4>RKAP Cement Mill</h4>
                            <p>Target RKAP Cement Mill dari Januari sampai Desember.</p>
                        </div>

                        <div class="rkap-group-total">
                            {{ number_format($totalCementMill ?? 0, 2, ',', '.') }} Ton
                        </div>
                    </div>

                    <div class="rkap-month-grid">
                        @foreach ($monthNames as $monthNumber => $monthName)
                            @php
                                $cementMillValue = optional($cementMillRakps->get($monthNumber))->value;
                            @endphp

                            <div class="rkap-month-card">
                                <div class="rkap-month-top">
                                    <label>{{ $monthName }}</label>
                                </div>

                                <div class="rkap-input-box">
                                    <input type="text" inputmode="decimal" name="cement_mill[{{ $monthNumber }}]"
                                        value="{{ old('cement_mill.' . $monthNumber, $cementMillValue ? number_format($cementMillValue, 2, ',', '.') : '') }}"
                                        placeholder="0,00">
                                    <small>Ton</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rkap-group-card">
                    <div class="rkap-group-head">
                        <div>
                            <h4>RKAP Packer</h4>
                            <p>Target RKAP Packer dari Januari sampai Desember.</p>
                        </div>

                        <div class="rkap-group-total">
                            {{ number_format($totalPacker ?? 0, 2, ',', '.') }} Ton
                        </div>
                    </div>

                    <div class="rkap-month-grid">
                        @foreach ($monthNames as $monthNumber => $monthName)
                            @php
                                $packerValue = optional($packerRakps->get($monthNumber))->value;
                            @endphp

                            <div class="rkap-month-card">
                                <div class="rkap-month-top">
                                    <label>{{ $monthName }}</label>
                                </div>

                                <div class="rkap-input-box">
                                    <input type="text" inputmode="decimal" name="packer[{{ $monthNumber }}]"
                                        value="{{ old('packer.' . $monthNumber, $packerValue ? number_format($packerValue, 2, ',', '.') : '') }}"
                                        placeholder="0,00">
                                    <small>Ton</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <br>
        <div class="rkap-page">
            <div class="rkap-result-section">
                <div class="rkap-result-header" style="text-align: center;">
                    <div>
                        <h4>Rekap Hasil RKAP Tahun {{ $year }}</h4>
                    </div>
                </div>

                <div class="rkap-result-list">
                    <details class="rkap-result-details">
                        <summary class="rkap-result-item">
                            <div class="rkap-result-left">
                                <div class="rkap-result-icon">
                                    <i class="bi bi-gear-wide-connected"></i>
                                </div>

                                <div>
                                    <h5>RKAP Cement Mill</h5>
                                    <p>Rekap target Cement Mill per bulan</p>
                                </div>
                            </div>

                            <div class="rkap-result-right">
                                <span>12 bulan</span>
                                <strong>{{ number_format($totalCementMill ?? 0, 2, ',', '.') }} Ton</strong>
                                <div class="rkap-eye">
                                    <i class="bi bi-eye"></i>
                                </div>
                            </div>
                        </summary>

                        <div class="rkap-detail-box">
                            <div class="rkap-detail-title">Detail RKAP Cement Mill</div>

                            <div class="rkap-result-table-wrap">
                                <table class="rkap-result-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>RKAP Cement Mill</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthNames as $monthNumber => $monthName)
                                            @php
                                                $cementMillValue =
                                                    optional($cementMillRakps->get($monthNumber))->value ?? 0;
                                            @endphp

                                            <tr>
                                                <td class="rkap-month-name">{{ $monthName }}</td>
                                                <td>
                                                    <div class="rkap-result-value">
                                                        <span>{{ number_format($cementMillValue, 2, ',', '.') }}</span>
                                                        <small>Ton</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="rkap-month-name">Total Per Tahun</th>
                                            <th>
                                                <div class="rkap-result-value">
                                                    <span>{{ number_format($totalCementMill ?? 0, 2, ',', '.') }}</span>
                                                    <small>Ton</small>
                                                </div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </details>

                    <details class="rkap-result-details">
                        <summary class="rkap-result-item">
                            <div class="rkap-result-left">
                                <div class="rkap-result-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>

                                <div>
                                    <h5>RKAP Packer</h5>
                                    <p>Rekap target Packer per bulan</p>
                                </div>
                            </div>

                            <div class="rkap-result-right">
                                <span>12 bulan</span>
                                <strong>{{ number_format($totalPacker ?? 0, 2, ',', '.') }} Ton</strong>
                                <div class="rkap-eye">
                                    <i class="bi bi-eye"></i>
                                </div>
                            </div>
                        </summary>

                        <div class="rkap-detail-box">
                            <div class="rkap-detail-title">Detail RKAP Packer</div>

                            <div class="rkap-result-table-wrap">
                                <table class="rkap-result-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>RKAP Packer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthNames as $monthNumber => $monthName)
                                            @php
                                                $packerValue = optional($packerRakps->get($monthNumber))->value ?? 0;
                                            @endphp

                                            <tr>
                                                <td class="rkap-month-name">{{ $monthName }}</td>
                                                <td>
                                                    <div class="rkap-result-value">
                                                        <span>{{ number_format($packerValue, 2, ',', '.') }}</span>
                                                        <small>Ton</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="rkap-month-name">Total Per Tahun</th>
                                            <th>
                                                <div class="rkap-result-value">
                                                    <span>{{ number_format($totalPacker ?? 0, 2, ',', '.') }}</span>
                                                    <small>Ton</small>
                                                </div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </details>

                    <details class="rkap-result-details">
                        <summary class="rkap-result-item">
                            <div class="rkap-result-left">
                                <div class="rkap-result-icon">
                                    <i class="bi bi-clipboard-data"></i>
                                </div>

                                <div>
                                    <h5>Total RKAP Tahunan</h5>
                                    <p>Gabungan Cement Mill dan Packer</p>
                                </div>
                            </div>

                            <div class="rkap-result-right">
                                <span>2 kategori</span>
                                <strong>{{ number_format($grandTotal ?? 0, 2, ',', '.') }} Ton</strong>
                                <div class="rkap-eye">
                                    <i class="bi bi-eye"></i>
                                </div>
                            </div>
                        </summary>

                        <div class="rkap-detail-box">
                            <div class="rkap-detail-title">Detail Total RKAP Bulanan</div>

                            <div class="rkap-result-table-wrap">
                                <table class="rkap-result-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>RKAP Cement Mill</th>
                                            <th>RKAP Packer</th>
                                            <th>Total Bulanan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthNames as $monthNumber => $monthName)
                                            @php
                                                $cementMillValue =
                                                    optional($cementMillRakps->get($monthNumber))->value ?? 0;
                                                $packerValue = optional($packerRakps->get($monthNumber))->value ?? 0;
                                                $monthlyTotal = $cementMillValue + $packerValue;
                                            @endphp

                                            <tr>
                                                <td class="rkap-month-name">{{ $monthName }}</td>
                                                <td>
                                                    <div class="rkap-result-value">
                                                        <span>{{ number_format($cementMillValue, 2, ',', '.') }}</span>
                                                        <small>Ton</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="rkap-result-value">
                                                        <span>{{ number_format($packerValue, 2, ',', '.') }}</span>
                                                        <small>Ton</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="rkap-result-value">
                                                        <span>{{ number_format($monthlyTotal, 2, ',', '.') }}</span>
                                                        <small>Ton</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="rkap-month-name">Total Per Tahun</th>
                                            <th>
                                                <div class="rkap-result-value">
                                                    <span>{{ number_format($totalCementMill ?? 0, 2, ',', '.') }}</span>
                                                    <small>Ton</small>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="rkap-result-value">
                                                    <span>{{ number_format($totalPacker ?? 0, 2, ',', '.') }}</span>
                                                    <small>Ton</small>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="rkap-result-value">
                                                    <span>{{ number_format($grandTotal ?? 0, 2, ',', '.') }}</span>
                                                    <small>Ton</small>
                                                </div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>



        <div class="form-section rkap-action-card">
            <div class="form-actions">
                @if ($canManageRakp)
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i> Simpan RKAP
                    </button>
                @else
                    <button type="button" class="btn-secondary" disabled>
                        <i class="bi bi-eye"></i> Mode Viewer
                    </button>
                @endif

                <a href="{{ route('rakps.export', ['year' => $year]) }}" class="btn-secondary">
                    <i class="bi bi-file-earmark-excel"></i> Export RKAP
                </a>

                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </form>
@endsection

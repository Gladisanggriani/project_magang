@extends('layouts.main')

@section('title', 'RAKP')

@section('content')
    <div class="page-card rakp-hero">
        <div>
            <h2 class="page-card-title">Input RAKP</h2>
            <p class="page-card-subtitle">
                Input data RAKP bulanan sebagai acuan perhitungan ketahanan stock.
            </p>
        </div>
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

    <form action="{{ route('rakps.store') }}" method="POST">
        @csrf

        <div class="form-section rakp-main-card">
            <div class="rakp-section-header">
                <div class="rakp-section-left">
                    <div class="form-section-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h3>RAKP Bulanan</h3>
                        <p>Isi nilai RAKP per bulan. Kosongkan bulan yang belum memiliki data.</p>
                    </div>
                </div>

                <div class="rakp-year-control">
                    <label>Tahun</label>
                    <input
                        type="number"
                        name="year"
                        value="{{ old('year', $year) }}"
                        min="2000"
                        max="2100"
                        required
                    >
                </div>
            </div>

            <div class="input-note rakp-note">
                Gunakan format angka Indonesia, contoh: 90.000,00.
            </div>

            <div class="rakp-month-grid">
                @foreach ($monthNames as $monthNumber => $monthName)
                    @php
                        $value = optional($rakps->get($monthNumber))->value;
                    @endphp

                    <div class="rakp-month-card">
                        <div class="rakp-month-top">
                            <span class="rakp-month-number">{{ str_pad($monthNumber, 2, '0', STR_PAD_LEFT) }}</span>
                            <label>{{ $monthName }}</label>
                        </div>

                        <div class="rakp-input-wrap">
                            <input
                                type="text"
                                inputmode="decimal"
                                name="rakps[{{ $monthNumber }}]"
                                value="{{ old('rakps.' . $monthNumber, $value ? number_format($value, 2, ',', '.') : '') }}"
                                placeholder="0,00"
                            >
                            <span>Ton</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-section rakp-action-card">
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-save"></i> Simpan RAKP
                </button>

                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </form>
@endsection
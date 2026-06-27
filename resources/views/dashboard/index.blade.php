@extends('layouts.main')

@section('title', 'Dashboard Operasional')

@section('content')
    @if (!$todayReport)
        <div class="empty-state">
            <h3>Belum ada data laporan</h3>
            <p>Silakan input laporan harian terlebih dahulu agar dashboard dapat menampilkan data operasional.</p>

            @auth
                @if (auth()->user()->hasRole(['admin', 'operator']))
                    <a href="{{ route('reports.create') }}" class="btn-action">
                        <i class="bi bi-plus-circle"></i> Input Laporan Pertama
                    </a>
                @endif
            @else
                <p>Silakan login sebagai admin/operator untuk melakukan input laporan.</p>

                <a href="{{ route('login') }}" class="btn-action">
                    <i class="bi bi-box-arrow-in-right"></i> Login Admin/Operator
                </a>
            @endauth
        </div>
    @else
        @php
            $statusClass = function ($status) {
                return match (strtoupper($status ?? '')) {
                    'RUN', 'READY' => 'status-badge status-success',
                    'STOP', 'TROUBLE' => 'status-badge status-danger',
                    'MAINTENANCE' => 'status-badge status-warning',
                    default => 'status-badge status-neutral',
                };
            };

            $totalTruck = ($todayReport->truck_packer_area ?? 0) + ($todayReport->truck_emplacement_area ?? 0);

            $stockCount = $todayReport->materialStocks->count();
            $stockTotal = $todayReport->materialStocks->sum('quantity');

            $receiptCount = $todayReport->materialReceipts->count();
            $receiptTotal = $todayReport->materialReceipts->sum('quantity');

            $intransitCount = $todayReport->materialIntransits->count();
            $intransitTotal = $todayReport->materialIntransits->sum('quantity');

            $usageCount = $todayReport->materialUsages->count();
            $usageTotal = $todayReport->materialUsages->sum('quantity');

            $resistanceCount = isset($stockResistance) ? count($stockResistance) : 0;

            $bagCount = $todayReport->bagStocks->count();
            $bagTotal = $todayReport->bagStocks->sum('quantity');
        @endphp

        <section class="hero-banner">
            <div>
                <h1 class="hero-title">OPERASIONAL GP DUMAI</h1>

                <p class="hero-subtitle">
                    @if (\Carbon\Carbon::parse($todayReport->report_date)->toDateString() !== now()->toDateString())
                        Catatan: belum ada laporan untuk hari ini, dashboard menampilkan data laporan terakhir.
                    @endif
                </p>
            </div>

            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <div class="hero-date">
                    <i class="bi bi-calendar-event"></i>
                    Data laporan: {{ \Carbon\Carbon::parse($todayReport->report_date)->format('d F Y') }}
                </div>

                @auth
                    @if (auth()->user()->hasRole(['admin', 'operator']))
                        <a href="{{ route('reports.create') }}" class="btn-action">
                            <i class="bi bi-plus-circle"></i>
                            Input Laporan
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-action">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login Admin/Operator
                    </a>
                @endauth
            </div>
        </section>

        <section class="stats-grid stats-grid-desktop-5">
            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Produksi Semen</p>
                        <h3 class="stat-value">
                            {{ number_format($todayReport->production_cm ?? 0, 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>

                    <span class="stat-badge badge-red">Hari Ini</span>
                </div>

                <div class="stat-meta">
                    <span>MTD: {{ number_format($mtdProductionCm ?? 0, 2, ',', '.') }} Ton</span>
                    <span>Avg: {{ number_format($avgProductionCm ?? 0, 2, ',', '.') }} Ton</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Semen Dari Kapal</p>
                        <h3 class="stat-value">
                            {{ number_format($todayReport->production_ship ?? 0, 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>

                    <span class="stat-badge badge-orange">Kapal</span>
                </div>

                <div class="stat-meta">
                    <span>Hari ini</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Produksi Packer</p>
                        <h3 class="stat-value">
                            {{ number_format($todayReport->production_packer ?? 0, 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>

                    <span class="stat-badge badge-blue">Packer</span>
                </div>

                <div class="stat-meta">
                    <span>MTD: {{ number_format($mtdProductionPacker ?? 0, 2, ',', '.') }} Ton</span>
                    <span>Avg: {{ number_format($avgProductionPacker ?? 0, 2, ',', '.') }} Ton</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Closing Stock</p>
                        <h3 class="stat-value">
                            {{ number_format($closingStock ?? 0, 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>

                    <span class="stat-badge badge-yellow">Stock</span>
                </div>

                <div class="stat-meta">
                    <span>Hari ini</span>
                </div>
            </article>

            <article class="stat-card stat-card-queue">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Antrian Truck</p>
                        <h3 class="stat-value">
                            {{ $totalTruck ?? 0 }}
                            <span>Truck</span>
                        </h3>
                    </div>

                    <span class="stat-badge badge-purple">Queue</span>
                </div>

                <div class="stat-meta">
                    <span>Packer: {{ $todayReport->truck_packer_area ?? 0 }}</span>
                    <span>Emplacement: {{ $todayReport->truck_emplacement_area ?? 0 }}</span>
                </div>
            </article>
        </section>


        <section class="dashboard-content">
            <div class="panel-card span-8">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Grafik Produksi Bulan Ini</h3>
                        {{-- REVISI: Mengubah deskripsi untuk 2 Packer --}}
                        <p class="panel-subtitle">Perbandingan produksi Cement Mill, Packer 1, dan Packer 2 per hari</p>
                    </div>
                </div>

                <div class="chart-box">
                    <canvas id="productionChart"></canvas>
                </div>
            </div>

            <div class="panel-card span-4">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Status Mesin</h3>
                        <p class="panel-subtitle">Status operasional utama hari ini</p>
                    </div>
                </div>

                <div class="machine-list">
                    <div class="machine-item">
                        <div class="machine-meta">
                            <h4>Cement Mill</h4>
                            <p>{{ $todayReport->cement_mill_note ?: 'Tidak ada keterangan' }}</p>
                        </div>

                        <span class="{{ $statusClass($todayReport->cement_mill_status) }}">
                            {{ $todayReport->cement_mill_status ?: '-' }}
                        </span>
                    </div>

                    <div class="machine-item">
                        <div class="machine-meta">
                            <h4>Packer 1</h4>
                            <p>{{ $todayReport->packer1_note ?: 'Tidak ada keterangan' }}</p>
                        </div>

                        <span class="{{ $statusClass($todayReport->packer1_status) }}">
                            {{ $todayReport->packer1_status ?: '-' }}
                        </span>
                    </div>

                    <div class="machine-item">
                        <div class="machine-meta">
                            <h4>Packer 2</h4>
                            <p>{{ $todayReport->packer2_note ?: 'Tidak ada keterangan' }}</p>
                        </div>

                        <span class="{{ $statusClass($todayReport->packer2_status) }}">
                            {{ $todayReport->packer2_status ?: '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="panel-card span-12">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Monitoring Level Silo Semen</h3>
                        <p class="panel-subtitle"></p>
                    </div>

                    <div class="silo-last-update">
                        Update otomatis: <span id="silo-last-update-time">-</span>
                    </div>
                </div>

                <div class="premium-silo-grid">
                    @forelse($silos ?? [] as $silo)
                        @php
                            $trendLabel = match ($silo['trend']) {
                                'up' => 'Naik',
                                'down' => 'Turun',
                                default => 'Stabil',
                            };
                        @endphp

                        <div class="premium-silo-card" data-silo-code="{{ $silo['code'] }}">
                            <div class="premium-silo-card-top">
                                <div>
                                    <h4 class="premium-silo-name">{{ $silo['label'] }}</h4>
                                    <p class="premium-silo-desc">Visual level kapasitas silo</p>
                                </div>

                                <div class="premium-silo-badges">
                                    <span class="silo-chip silo-level-chip {{ $silo['level_class'] }}"
                                        data-field="level_text">
                                        Level: {{ $silo['level_text'] }}
                                    </span>

                                    <span class="silo-chip silo-trend-chip trend-{{ $silo['trend'] }}"
                                        data-field="trend_text">
                                        Trend: {{ $trendLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="premium-silo-body">
                                <div class="premium-silo-visual-wrap">
                                    <div class="premium-silo-scale">
                                        <span>100%</span>
                                        <span>75%</span>
                                        <span>50%</span>
                                        <span>25%</span>
                                        <span>0%</span>
                                    </div>

                                    <div class="premium-silo-visual">
                                        <div class="premium-silo-cap"></div>

                                        <div class="premium-silo-shell">
                                            <div class="premium-silo-fill {{ $silo['level_class'] }}" data-field="fill"
                                                style="height: {{ $silo['percentage'] }}%;">
                                                <div class="premium-silo-wave"></div>
                                                <div class="premium-silo-percent-label" data-field="percentage_badge">
                                                    {{ $silo['formatted_percentage'] }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="premium-silo-cone"></div>

                                        <div class="premium-silo-legs">
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="premium-silo-stats">
                                    <div class="premium-silo-stat-box">
                                        <span class="stat-label">Isi Saat Ini</span>
                                        <strong class="stat-value" data-field="value">
                                            {{ $silo['formatted_value'] }}
                                        </strong>
                                    </div>

                                    <div class="premium-silo-stat-box">
                                        <span class="stat-label">Kapasitas</span>
                                        <strong class="stat-value" data-field="capacity">
                                            {{ $silo['formatted_capacity'] }}
                                        </strong>
                                    </div>

                                    <div class="premium-silo-stat-box">
                                        <span class="stat-label">Level</span>
                                        <strong class="stat-value" data-field="percentage">
                                            {{ $silo['formatted_percentage'] }}
                                        </strong>
                                    </div>

                                    <div class="premium-silo-stat-box">
                                        <span class="stat-label">Selisih</span>
                                        <strong class="stat-value" data-field="delta">
                                            @if ($silo['trend'] === 'up')
                                                +{{ $silo['formatted_delta'] }}
                                            @elseif($silo['trend'] === 'down')
                                                -{{ $silo['formatted_delta'] }}
                                            @else
                                                0,00 {{ $silo['unit'] }}
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="premium-silo-empty">
                            Belum ada data silo untuk ditampilkan.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="panel-card span-4">
                <button type="button" class="summary-popup-card" data-modal-title="Closing Stock Material"
                    data-modal-subtitle="Data stock material terakhir" data-modal-target="#modal-content-closing-stock">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon red">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Closing Stock Material</h3>
                            <p class="summary-popup-subtitle">Data stock material terakhir</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>{{ $stockCount }} item</span>
                            <strong>{{ number_format($stockTotal, 2, ',', '.') }}</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>

            <div class="panel-card span-4">
                <button type="button" class="summary-popup-card" data-modal-title="Penerimaan Material"
                    data-modal-subtitle="Data material masuk 00.00 s/d 24.00" data-modal-target="#modal-content-receipt">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon blue">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Penerimaan Material</h3>
                            <p class="summary-popup-subtitle">Data material masuk 00.00 s/d 24.00</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>{{ $receiptCount }} item</span>
                            <strong>{{ number_format($receiptTotal, 2, ',', '.') }}</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>

            <div class="panel-card span-4">
                <button type="button" class="summary-popup-card" data-modal-title="Intransit Material"
                    data-modal-subtitle="Data intransit material yang diinput manual"
                    data-modal-target="#modal-content-intransit">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon yellow">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Intransit Material</h3>
                            <p class="summary-popup-subtitle">Data intransit material</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>{{ $intransitCount }} item</span>
                            <strong>{{ number_format($intransitTotal, 2, ',', '.') }}</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>

            <div class="panel-card span-4">
                <button type="button" class="summary-popup-card" data-modal-title="Pemakaian / Pengeluaran Material"
                    data-modal-subtitle="Data material terpakai 00.00 s/d 24.00" data-modal-target="#modal-content-usage">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon green">
                            <i class="bi bi-arrow-up-circle"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Pemakaian / Pengeluaran</h3>
                            <p class="summary-popup-subtitle">Data material terpakai 00.00 s/d 24.00</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>{{ $usageCount }} item</span>
                            <strong>{{ number_format($usageTotal, 2, ',', '.') }}</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>

            <div class="panel-card span-4">
                <button type="button" class="summary-popup-card" data-modal-title="Ketahanan Stock Material"
                    data-modal-subtitle="Estimasi ketahanan berdasarkan rata-rata pemakaian"
                    data-modal-target="#modal-content-resistance">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon purple">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Ketahanan Stock Material</h3>
                            <p class="summary-popup-subtitle">Estimasi hari stock bertahan</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>{{ $resistanceCount }} item</span>
                            <strong>Hari</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>

            <div class="panel-card span-4">
                <button type="button" class="summary-popup-card" data-modal-title="Parameter Cement Mill"
                    data-modal-subtitle="Ringkasan parameter operasional"
                    data-modal-target="#modal-content-cement-params">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon blue">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Cement Mill</h3>
                            <p class="summary-popup-subtitle">Ringkasan parameter operasional</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>6 parameter</span>
                            <strong>CM</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>

            <div class="panel-card span-4 center-single-card">
                <button type="button" class="summary-popup-card" data-modal-title="Stock Kantong"
                    data-modal-subtitle="Persediaan kantong per jenis" data-modal-target="#modal-content-bag-stock">
                    <div class="summary-popup-left">
                        <div class="summary-popup-icon red">
                            <i class="bi bi-bag"></i>
                        </div>
                        <div>
                            <h3 class="summary-popup-title">Stock Kantong</h3>
                            <p class="summary-popup-subtitle">Persediaan kantong per jenis</p>
                        </div>
                    </div>

                    <div class="summary-popup-right">
                        <div class="summary-popup-meta">
                            <span>{{ $bagCount }} jenis</span>
                            <strong>{{ number_format($bagTotal, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-popup-arrow">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                </button>
            </div>
        </section>

        <div style="display:none;">
            <div id="modal-content-closing-stock">
                @forelse($todayReport->materialStocks as $stock)
                    <div class="modal-data-row">
                        <div class="modal-data-left">
                            <span class="modal-data-dot red"></span>
                            <div>
                                <div class="modal-data-label">{{ $stock->material_name }}</div>
                                <div class="modal-data-subtext">Closing stock</div>
                            </div>
                        </div>
                        <div class="modal-data-value red">
                            {{ number_format($stock->quantity, 2, ',', '.') }} {{ $stock->unit }}
                        </div>
                    </div>
                @empty
                    <div class="modal-empty">Belum ada data closing stock.</div>
                @endforelse
            </div>

            <div id="modal-content-receipt">
                @forelse($todayReport->materialReceipts as $receipt)
                    <div class="modal-data-row">
                        <div class="modal-data-left">
                            <span class="modal-data-dot blue"></span>
                            <div>
                                <div class="modal-data-label">{{ $receipt->material_name }}</div>
                                <div class="modal-data-subtext">Material masuk</div>
                            </div>
                        </div>
                        <div class="modal-data-value blue">
                            {{ number_format($receipt->quantity, 2, ',', '.') }} {{ $receipt->unit }}
                        </div>
                    </div>
                @empty
                    <div class="modal-empty">Belum ada data penerimaan material.</div>
                @endforelse
            </div>

            <div id="modal-content-intransit">
                @forelse($todayReport->materialIntransits as $intransit)
                    <div class="modal-data-row">
                        <div class="modal-data-left">
                            <span class="modal-data-dot yellow"></span>
                            <div>
                                <div class="modal-data-label">{{ $intransit->material_name }}</div>
                                <div class="modal-data-subtext">Intransit material</div>
                            </div>
                        </div>

                        <div class="modal-data-value yellow">
                            {{ number_format($intransit->quantity, 2, ',', '.') }} {{ $intransit->unit }}
                        </div>
                    </div>
                @empty
                    <div class="modal-empty">Belum ada data intransit material.</div>
                @endforelse
            </div>

            <div id="modal-content-usage">
                @forelse($todayReport->materialUsages as $usage)
                    <div class="modal-data-row">
                        <div class="modal-data-left">
                            <span class="modal-data-dot green"></span>
                            <div>
                                <div class="modal-data-label">{{ $usage->material_name }}</div>
                                <div class="modal-data-subtext">Material terpakai</div>
                            </div>
                        </div>
                        <div class="modal-data-value green">
                            {{ number_format($usage->quantity, 2, ',', '.') }} {{ $usage->unit }}
                        </div>
                    </div>
                @empty
                    <div class="modal-empty">Belum ada data pemakaian material.</div>
                @endforelse
            </div>

            <div id="modal-content-resistance">
                @forelse($stockResistance ?? [] as $item)
                    <div class="modal-data-row">
                        <div class="modal-data-left">
                            <span class="modal-data-dot purple"></span>
                            <div>
                                <div class="modal-data-label">{{ $item['material_name'] }}</div>
                                <div class="modal-data-subtext">
                                    Stock: {{ number_format($item['stock'], 2, ',', '.') }} {{ $item['unit'] }}
                                    • Pemakaian hari ini: {{ number_format($item['today_usage'], 2, ',', '.') }}
                                    {{ $item['unit'] }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-data-value purple">
                            @if ($item['resistance_days'] > 0)
                                {{ number_format($item['resistance_days'], 1, ',', '.') }} Hari
                            @else
                                -
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="modal-empty">Belum ada data ketahanan stock material.</div>
                @endforelse
            </div>

            <div id="modal-content-bag-stock">
                @forelse($todayReport->bagStocks as $bag)
                    <div class="modal-data-row">
                        <div class="modal-data-left">
                            <span class="modal-data-dot red"></span>
                            <div>
                                <div class="modal-data-label">{{ $bag->bag_type }}</div>
                                <div class="modal-data-subtext">Jenis kantong</div>
                            </div>
                        </div>
                        <div class="modal-data-value red">
                            {{ number_format($bag->quantity, 0, ',', '.') }} {{ $bag->unit }}
                        </div>
                    </div>
                @empty
                    <div class="modal-empty">Belum ada data stock kantong.</div>
                @endforelse
            </div>

            <div id="modal-content-cement-params">
                <div class="modal-data-row">
                    <div class="modal-data-left">
                        <span class="modal-data-dot blue"></span>
                        <div>
                            <div class="modal-data-label">Feed</div>
                            <div class="modal-data-subtext">Parameter umpan</div>
                        </div>
                    </div>
                    <div class="modal-data-value blue">
                        {{ number_format($todayReport->feed ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="modal-data-row">
                    <div class="modal-data-left">
                        <span class="modal-data-dot blue"></span>
                        <div>
                            <div class="modal-data-label">Blaine</div>
                            <div class="modal-data-subtext">Kehalusan semen</div>
                        </div>
                    </div>
                    <div class="modal-data-value blue">
                        {{ number_format($todayReport->blaine ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="modal-data-row">
                    <div class="modal-data-left">
                        <span class="modal-data-dot blue"></span>
                        <div>
                            <div class="modal-data-label">Sieving</div>
                            <div class="modal-data-subtext">Nilai saringan</div>
                        </div>
                    </div>
                    <div class="modal-data-value blue">
                        {{ number_format($todayReport->sieving ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="modal-data-row">
                    <div class="modal-data-left">
                        <span class="modal-data-dot yellow"></span>
                        <div>
                            <div class="modal-data-label">Running Hours</div>
                            <div class="modal-data-subtext">Jam operasi</div>
                        </div>
                    </div>
                    <div class="modal-data-value yellow">
                        {{ number_format($todayReport->running_hours ?? 0, 2, ',', '.') }} Jam
                    </div>
                </div>

                <div class="modal-data-row">
                    <div class="modal-data-left">
                        <span class="modal-data-dot purple"></span>
                        <div>
                            <div class="modal-data-label">Clinker Factor</div>
                            <div class="modal-data-subtext">Faktor clinker</div>
                        </div>
                    </div>
                    <div class="modal-data-value purple">
                        {{ number_format($todayReport->clinker_factor ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="modal-data-row">
                    <div class="modal-data-left">
                        <span class="modal-data-dot green"></span>
                        <div>
                            <div class="modal-data-label">Silo Semen</div>
                            <div class="modal-data-subtext">Persediaan di silo</div>
                        </div>
                    </div>
                    <div class="modal-data-value green">
                        {{ number_format($todayReport->silo_semen ?? 0, 2, ',', '.') }} Ton
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-modal" id="dashboardModal">
            <div class="dashboard-modal-overlay" id="dashboardModalOverlay"></div>

            <div class="dashboard-modal-dialog">
                <div class="dashboard-modal-header">
                    <div>
                        <h3 id="dashboardModalTitle">Detail Data</h3>
                        <p id="dashboardModalSubtitle">Informasi detail</p>
                    </div>

                    <button type="button" class="dashboard-modal-close" id="dashboardModalClose">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="dashboard-modal-body" id="dashboardModalBody"></div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartCanvas = document.getElementById('productionChart');

            if (chartCanvas) {
                const chartLabels = @json(($chartReports ?? collect())->pluck('report_date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M')));
                const productionCm = @json(($chartReports ?? collect())->pluck('production_cm'));

                // REVISI: Memecah data packer menjadi dua variabel terpisah.
                // Pastikan nama kolom 'production_packer1' dan 'production_packer2' sesuai dengan di database/controller kamu.
                const productionPacker1 = @json(($chartReports ?? collect())->pluck('production_packer1'));
                const productionPacker2 = @json(($chartReports ?? collect())->pluck('production_packer2'));

                new Chart(chartCanvas, {
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Produksi Cement Mill',
                                data: productionCm,
                                backgroundColor: 'rgba(215, 25, 32, 0.70)',
                                borderRadius: 10,
                                maxBarThickness: 32
                            },
                            // REVISI: Dataset untuk Packer 1 (Garis Biru)
                            {
                                type: 'line',
                                label: 'Produksi Packer 1',
                                data: productionPacker1,
                                borderColor: 'rgba(37, 99, 235, 1)',
                                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                                tension: 0.35,
                                fill: false,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            // REVISI: Dataset untuk Packer 2 (Garis Hijau)
                            {
                                type: 'line',
                                label: 'Produksi Packer 2',
                                data: productionPacker2,
                                borderColor: 'rgba(25, 135, 84, 1)',
                                backgroundColor: 'rgba(25, 135, 84, 0.15)',
                                tension: 0.35,
                                fill: false,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'start'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e5e7eb'
                                }
                            }
                        }
                    }
                });
            }

            const siloUrl = "{{ route('dashboard.silo-data') }}";
            const lastUpdateEl = document.getElementById('silo-last-update-time');

            function updateTrendClass(el, trend) {
                el.classList.remove('trend-up', 'trend-down', 'trend-stable');
                el.classList.add('trend-' + trend);
            }

            function updateLevelClass(el, levelClass) {
                el.classList.remove('level-low', 'level-medium', 'level-high');
                el.classList.add(levelClass);
            }

            function trendText(trend) {
                if (trend === 'up') return 'Trend: Naik';
                if (trend === 'down') return 'Trend: Turun';
                return 'Trend: Stabil';
            }

            function levelText(level) {
                return 'Level: ' + level;
            }

            async function refreshSiloData() {
                if (!lastUpdateEl) return;

                try {
                    const response = await fetch(siloUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) return;

                    const result = await response.json();

                    if (result.updated_at) {
                        lastUpdateEl.textContent = result.updated_at;
                    }

                    if (!result.silos || !Array.isArray(result.silos)) return;

                    result.silos.forEach((silo) => {
                        const card = document.querySelector(`[data-silo-code="${silo.code}"]`);
                        if (!card) return;

                        const fill = card.querySelector('[data-field="fill"]');
                        const levelTextEl = card.querySelector('[data-field="level_text"]');
                        const trendTextEl = card.querySelector('[data-field="trend_text"]');
                        const valueEl = card.querySelector('[data-field="value"]');
                        const capacityEl = card.querySelector('[data-field="capacity"]');
                        const percentageEl = card.querySelector('[data-field="percentage"]');
                        const percentageBadgeEl = card.querySelector('[data-field="percentage_badge"]');
                        const deltaEl = card.querySelector('[data-field="delta"]');

                        if (fill) {
                            fill.style.height = silo.percentage + '%';
                            updateLevelClass(fill, silo.level_class);
                        }

                        if (levelTextEl) {
                            levelTextEl.textContent = levelText(silo.level_text);
                            updateLevelClass(levelTextEl, silo.level_class);
                        }

                        if (trendTextEl) {
                            trendTextEl.textContent = trendText(silo.trend);
                            updateTrendClass(trendTextEl, silo.trend);
                        }

                        if (valueEl) valueEl.textContent = silo.formatted_value;
                        if (capacityEl) capacityEl.textContent = silo.formatted_capacity;
                        if (percentageEl) percentageEl.textContent = silo.formatted_percentage;
                        if (percentageBadgeEl) percentageBadgeEl.textContent = silo
                        .formatted_percentage;

                        if (deltaEl) {
                            if (silo.trend === 'up') {
                                deltaEl.textContent = '+' + silo.formatted_delta;
                            } else if (silo.trend === 'down') {
                                deltaEl.textContent = '-' + silo.formatted_delta;
                            } else {
                                deltaEl.textContent = '0,00 ' + silo.unit;
                            }
                        }
                    });
                } catch (error) {
                    console.error('Gagal memuat data silo:', error);
                }
            }

            refreshSiloData();
            setInterval(refreshSiloData, 10000);

            const modal = document.getElementById('dashboardModal');
            const modalOverlay = document.getElementById('dashboardModalOverlay');
            const modalClose = document.getElementById('dashboardModalClose');
            const modalTitle = document.getElementById('dashboardModalTitle');
            const modalSubtitle = document.getElementById('dashboardModalSubtitle');
            const modalBody = document.getElementById('dashboardModalBody');
            const triggers = document.querySelectorAll('.summary-popup-card');

            function openDashboardModal(title, subtitle, targetSelector) {
                if (!modal || !modalTitle || !modalSubtitle || !modalBody) return;

                const source = document.querySelector(targetSelector);
                if (!source) return;

                modalTitle.textContent = title || 'Detail Data';
                modalSubtitle.textContent = subtitle || '';
                modalBody.innerHTML = source.innerHTML;

                modal.classList.add('show');
                document.body.classList.add('modal-open');
            }

            function closeDashboardModal() {
                if (!modal || !modalBody) return;

                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
                modalBody.innerHTML = '';
            }

            triggers.forEach((trigger) => {
                trigger.addEventListener('click', function() {
                    openDashboardModal(
                        this.dataset.modalTitle,
                        this.dataset.modalSubtitle,
                        this.dataset.modalTarget
                    );
                });
            });

            if (modalClose) modalClose.addEventListener('click', closeDashboardModal);
            if (modalOverlay) modalOverlay.addEventListener('click', closeDashboardModal);

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal && modal.classList.contains('show')) {
                    closeDashboardModal();
                }
            });
        });
    </script>
@endpush

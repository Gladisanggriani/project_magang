@extends('layouts.main')

@section('title', 'Dashboard Operasional')

@section('content')
    @if (!$todayReport)
        <div class="empty-state">
            <h3>Belum ada data laporan</h3>
            <p>Silakan input laporan harian terlebih dahulu agar dashboard dapat menampilkan data operasional.</p>
            @if (auth()->user()->hasRole(['admin', 'operator']))
                <a href="{{ route('reports.create') }}" class="btn-action">
                    <i class="bi bi-plus-circle"></i> Input Laporan Pertama
                </a>
            @else
                <p>Silakan hubungi admin/operator untuk melakukan input laporan.</p>
            @endif
        </div>
    @else
        @php
            $getStock = fn($name) => optional($todayReport->materialStocks->firstWhere('material_name', $name))
                ->quantity ?? 0;

            $statusClass = function ($status) {
                return match (strtoupper($status ?? '')) {
                    'RUN', 'READY' => 'status-badge status-success',
                    'STOP', 'TROUBLE' => 'status-badge status-danger',
                    'MAINTENANCE' => 'status-badge status-warning',
                    default => 'status-badge status-neutral',
                };
            };

            $totalTruck = ($todayReport->truck_packer_area ?? 0) + ($todayReport->truck_emplacement_area ?? 0);
            $totalReceipt = $todayReport->materialReceipts->sum('quantity');
            $totalUsage = $todayReport->materialUsages->sum('quantity');
        @endphp

        <section class="hero-banner">
            <div>
                <h1 class="hero-title">OPERASIONAL GP DUMAI</h1>
                <p class="hero-subtitle">
                    Monitoring data produksi, stock material, status mesin, dan aktivitas packer harian.
                    @if (\Carbon\Carbon::parse($todayReport->report_date)->toDateString() !== now()->toDateString())
                        <br>
                        <small>
                            Catatan: belum ada laporan untuk hari ini, dashboard menampilkan data laporan terakhir.
                        </small>
                    @endif
                </p>
            </div>

            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <div class="hero-date">
                    <i class="bi bi-calendar-event"></i>
                    Data laporan: {{ \Carbon\Carbon::parse($todayReport->report_date)->format('d F Y') }}
                </div>

                @if (auth()->user()->hasRole(['admin', 'operator']))
                    <a href="{{ route('reports.create') }}" class="btn-action">
                        <i class="bi bi-plus-circle"></i> Input Laporan
                    </a>
                @endif
            </div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Produksi Semen </p>
                        <h3 class="stat-value">
                            {{ number_format($todayReport->production_cm, 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>
                    <span class="stat-badge badge-red">Hari Ini</span>
                </div>
                <div class="stat-meta">
                    <span>MTD: {{ number_format($mtdProductionCm, 2, ',', '.') }} Ton</span>
                    <span>Avg: {{ number_format($avgProductionCm, 2, ',', '.') }} Ton</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Produksi Packer</p>
                        <h3 class="stat-value">
                            {{ number_format($todayReport->production_packer, 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>
                    <span class="stat-badge badge-blue">Packer</span>
                </div>
                <div class="stat-meta">
                    <span>MTD: {{ number_format($mtdProductionPacker, 2, ',', '.') }} Ton</span>
                    <span>Avg: {{ number_format($avgProductionPacker, 2, ',', '.') }} Ton</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Closing Stock </p>
                        <h3 class="stat-value">
                            {{ number_format($getStock('Semen'), 2, ',', '.') }}
                            <span>Ton</span>
                        </h3>
                    </div>
                    <span class="stat-badge badge-yellow">Stock</span>
                </div>
                <div class="stat-meta">
                    <span>Silo: {{ number_format($todayReport->silo_semen, 2, ',', '.') }} Ton</span>
                    <span>Feed: {{ number_format($todayReport->feed, 2, ',', '.') }}</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-title">Antrian Truck</p>
                        <h3 class="stat-value">
                            {{ $totalTruck }}
                            <span>Truck</span>
                        </h3>
                    </div>
                    <span class="stat-badge badge-purple">Queue</span>
                </div>
                <div class="stat-meta">
                    <span>Packer: {{ $todayReport->truck_packer_area }}</span>
                    <span>Emplacement: {{ $todayReport->truck_emplacement_area }}</span>
                </div>
            </article>
        </section>

        <section class="dashboard-content">
            <div class="panel-card span-8">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Grafik Produksi Bulan Ini</h3>
                        <p class="panel-subtitle">Perbandingan produksi Cement Mill dan produksi Packer per hari</p>
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

            <div class="panel-card span-4">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Closing Stock Material</h3>
                        <p class="panel-subtitle">Data stock material terakhir</p>
                    </div>
                </div>

                <div class="material-list">
                    @forelse($todayReport->materialStocks as $stock)
                        <div class="material-item">
                            <div class="material-left">
                                <div class="material-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <div class="material-name">{{ $stock->material_name }}</div>
                                    <div class="material-subtext">Closing stock</div>
                                </div>
                            </div>

                            <div class="material-value">
                                {{ number_format($stock->quantity, 2, ',', '.') }} {{ $stock->unit }}
                            </div>
                        </div>
                    @empty
                        <div class="material-empty">Belum ada data stock.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel-card span-4">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Penerimaan Material</h3>
                        <p class="panel-subtitle">
                            Data 00.00 s/d 24.00 • Total: {{ number_format($totalReceipt, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="material-list">
                    @forelse($todayReport->materialReceipts as $receipt)
                        <div class="material-item">
                            <div class="material-left">
                                <div class="material-icon">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                                <div>
                                    <div class="material-name">{{ $receipt->material_name }}</div>
                                    <div class="material-subtext">Material masuk</div>
                                </div>
                            </div>

                            <div class="material-value blue">
                                {{ number_format($receipt->quantity, 2, ',', '.') }} {{ $receipt->unit }}
                            </div>
                        </div>
                    @empty
                        <div class="material-empty">Belum ada data penerimaan.</div>
                    @endforelse
                </div>
            </div>
            <div class="panel-card span-4">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Intransit Material</h3>
                        <p class="panel-subtitle">
                            Berdasarkan penerimaan material 00.00 s/d 24.00
                        </p>
                    </div>
                </div>

                <div class="material-list">
                    @forelse($todayReport->materialReceipts as $receipt)
                        <div class="material-item">
                            <div class="material-left">
                                <div class="material-icon">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div>
                                    <div class="material-name">{{ $receipt->material_name }}</div>
                                    <div class="material-subtext">Intransit material</div>
                                </div>
                            </div>

                            <div class="material-value yellow">
                                {{ number_format($receipt->quantity, 2, ',', '.') }} {{ $receipt->unit }}
                            </div>
                        </div>
                    @empty
                        <div class="material-empty">Belum ada data intransit material.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel-card span-4">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Pemakaian/Pengeluaran</h3>
                        <p class="panel-subtitle">
                            Total pemakaian: {{ number_format($totalUsage, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="material-list">
                    @forelse($todayReport->materialUsages as $usage)
                        <div class="material-item">
                            <div class="material-left">
                                <div class="material-icon">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </div>
                                <div>
                                    <div class="material-name">{{ $usage->material_name }}</div>
                                    <div class="material-subtext">Material terpakai</div>
                                </div>
                            </div>

                            <div class="material-value green">
                                {{ number_format($usage->quantity, 2, ',', '.') }} {{ $usage->unit }}
                            </div>
                        </div>
                    @empty
                        <div class="material-empty">Belum ada data pemakaian.</div>
                    @endforelse
                </div>
            </div>
            <div class="panel-card span-6">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Ketahanan Stock</h3>
                        <p class="panel-subtitle">
                            Estimasi berdasarkan rata-rata pemakaian bulan berjalan
                        </p>
                    </div>
                </div>

                <div class="material-list">
                    @forelse($stockResistance as $item)
                        <div class="material-item">
                            <div class="material-left">
                                <div class="material-icon">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <div class="material-name">{{ $item['material_name'] }}</div>
                                    <div class="material-subtext">
                                        Stock: {{ number_format($item['stock'], 2, ',', '.') }} {{ $item['unit'] }}
                                        • Avg: {{ number_format($item['average_usage'], 2, ',', '.') }}/hari
                                    </div>
                                </div>
                            </div>

                            <div class="material-value">
                                @if ($item['resistance_days'] > 0)
                                    {{ number_format($item['resistance_days'], 1, ',', '.') }} Hari
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="material-empty">Belum ada data ketahanan stock.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel-card span-6">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Stock Kantong</h3>
                        <p class="panel-subtitle">Persediaan kantong per jenis</p>
                    </div>
                </div>

                <div class="pretty-list">
                    @forelse($todayReport->bagStocks as $bag)
                        <div class="pretty-list-item">
                            <div class="pretty-list-left">
                                <div class="pretty-icon soft-red">
                                    <i class="bi bi-bag"></i>
                                </div>
                                <div>
                                    <div class="pretty-label">{{ $bag->bag_type }}</div>
                                    <div class="pretty-subtext">Jenis kantong</div>
                                </div>
                            </div>

                            <div class="pretty-pill">
                                {{ number_format($bag->quantity, 0, ',', '.') }} {{ $bag->unit }}
                            </div>
                        </div>
                    @empty
                        <div class="pretty-empty">
                            Belum ada data stock kantong.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="panel-card span-6">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Parameter Cement Mill</h3>
                        <p class="panel-subtitle">Ringkasan parameter operasional</p>
                    </div>
                </div>

                <div class="pretty-list">
                    <div class="pretty-list-item">
                        <div class="pretty-list-left">
                            <div class="pretty-icon soft-blue">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <div>
                                <div class="pretty-label">Feed</div>
                                <div class="pretty-subtext">Parameter umpan</div>
                            </div>
                        </div>
                        <div class="pretty-pill">
                            {{ number_format($todayReport->feed, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="pretty-list-item">
                        <div class="pretty-list-left">
                            <div class="pretty-icon soft-blue">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div>
                                <div class="pretty-label">Blaine</div>
                                <div class="pretty-subtext">Kehalusan semen</div>
                            </div>
                        </div>
                        <div class="pretty-pill">
                            {{ number_format($todayReport->blaine, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="pretty-list-item">
                        <div class="pretty-list-left">
                            <div class="pretty-icon soft-blue">
                                <i class="bi bi-bar-chart-line"></i>
                            </div>
                            <div>
                                <div class="pretty-label">Sieving</div>
                                <div class="pretty-subtext">Nilai saringan</div>
                            </div>
                        </div>
                        <div class="pretty-pill">
                            {{ number_format($todayReport->sieving, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="pretty-list-item">
                        <div class="pretty-list-left">
                            <div class="pretty-icon soft-yellow">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="pretty-label">Running Hours</div>
                                <div class="pretty-subtext">Jam operasi</div>
                            </div>
                        </div>
                        <div class="pretty-pill">
                            {{ number_format($todayReport->running_hours, 2, ',', '.') }} Jam
                        </div>
                    </div>

                    <div class="pretty-list-item">
                        <div class="pretty-list-left">
                            <div class="pretty-icon soft-purple">
                                <i class="bi bi-sliders"></i>
                            </div>
                            <div>
                                <div class="pretty-label">Clinker Factor</div>
                                <div class="pretty-subtext">Faktor clinker</div>
                            </div>
                        </div>
                        <div class="pretty-pill">
                            {{ number_format($todayReport->clinker_factor, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="pretty-list-item">
                        <div class="pretty-list-left">
                            <div class="pretty-icon soft-green">
                                <i class="bi bi-database"></i>
                            </div>
                            <div>
                                <div class="pretty-label">Silo Semen</div>
                                <div class="pretty-subtext">Persediaan di silo</div>
                            </div>
                        </div>
                        <div class="pretty-pill">
                            {{ number_format($todayReport->silo_semen, 2, ',', '.') }} Ton
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            const chartLabels = @json($chartReports->pluck('report_date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M')));
            const productionCm = @json($chartReports->pluck('production_cm'));
            const productionPacker = @json($chartReports->pluck('production_packer'));

            new Chart(document.getElementById('productionChart'), {
                data: {
                    labels: chartLabels,
                    datasets: [{
                            type: 'bar',
                            label: 'Produksi Cement Mill',
                            data: productionCm,
                            backgroundColor: 'rgba(215, 25, 32, 0.70)',
                            borderRadius: 10,
                            maxBarThickness: 32
                        },
                        {
                            type: 'line',
                            label: 'Produksi Packer',
                            data: productionPacker,
                            borderColor: 'rgba(37, 99, 235, 1)',
                            backgroundColor: 'rgba(37, 99, 235, 0.15)',
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
        </script>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        </script>
    @endif
@endsection

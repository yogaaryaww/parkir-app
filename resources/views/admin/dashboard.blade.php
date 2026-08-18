@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card primary h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total User</div>
                    <h3 class="fw-bold my-1">{{ $totalUser }}</h3>
                    <div class="small text-primary"><i class="fa-solid fa-users me-1"></i> Admin, Petugas, Owner</div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                    <i class="fa-solid fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card success h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total Area Parkir</div>
                    <h3 class="fw-bold my-1">{{ $totalArea }}</h3>
                    <div class="small text-success"><i class="fa-solid fa-layer-group me-1"></i> Master Area</div>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                    <i class="fa-solid fa-layer-group fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card info h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Transaksi Hari Ini</div>
                    <h3 class="fw-bold my-1">{{ $transaksiHariIni }}</h3>
                    <div class="small text-info"><i class="fa-solid fa-car me-1"></i> Kendaraan Masuk</div>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                    <i class="fa-solid fa-car-side fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card warning h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Pendapatan Hari Ini</div>
                    <h3 class="fw-bold my-1 text-truncate" style="max-width: 150px;">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                    <div class="small text-warning"><i class="fa-solid fa-money-bill-wave me-1"></i> Selesai Transaksi</div>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                    <i class="fa-solid fa-wallet fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Status Kapasitas Area Parkir -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-square-parking text-primary me-2"></i> Kapasitas Area Parkir</span>
                <a href="{{ route('admin.area.index') }}" class="btn btn-sm btn-outline-primary">Kelola Area</a>
            </div>
            <div class="card-body">
                @forelse($areaParkir as $area)
                    @php
                        $persen = $area->kapasitas > 0 ? round(($area->terisi / $area->kapasitas) * 100) : 0;
                        $colorClass = $persen >= 90 ? 'bg-danger' : ($persen >= 75 ? 'bg-warning' : 'bg-primary');
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">{{ $area->nama_area }}</span>
                            <span class="small text-muted">{{ $area->terisi }} / {{ $area->kapasitas }} Slot ({{ $persen }}%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar {{ $colorClass }}" role="progressbar" style="width: {{ $persen }}%" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3 m-0">Belum ada data area parkir.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Log Aktivitas Terakhir -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Log Aktivitas Terakhir</span>
                <a href="{{ route('admin.log.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logTerakhir as $log)
                                <tr>
                                    <td class="text-muted" style="white-space: nowrap;">{{ $log->created_at->format('d/m H:i') }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $log->user ? $log->user->nama : 'System' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary me-1">{{ $log->aktivitas }}</span>
                                        <span class="text-muted small">{{ Str::limit($log->deskripsi, 40) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada aktivitas tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

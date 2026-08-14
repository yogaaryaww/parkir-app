@extends('layouts.app')

@section('title', 'Dashboard Owner - Rekap Transaksi')

@section('content')

{{-- Kartu Ringkasan Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card info h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total Transaksi</div>
                    <h3 class="fw-bold my-1">{{ $totalTransaksi }}</h3>
                    <div class="small text-info">Periode ini</div>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                    <i class="fa-solid fa-list fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card warning h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Masih Parkir</div>
                    <h3 class="fw-bold my-1">{{ $totalMasukAktif }}</h3>
                    <div class="small text-warning">Kendaraan aktif</div>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                    <i class="fa-solid fa-car fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card success h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Transaksi Selesai</div>
                    <h3 class="fw-bold my-1">{{ $totalSelesai }}</h3>
                    <div class="small text-success">Sudah keluar</div>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                    <i class="fa-solid fa-circle-check fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card primary h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total Pendapatan</div>
                    <h3 class="fw-bold my-1 text-primary" style="font-size: 1.2rem;">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </h3>
                    <div class="small text-primary">Transaksi selesai</div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                    <i class="fa-solid fa-wallet fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Filter & Tombol Print --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('owner.dashboard') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control" value="{{ $tglMulai }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control" value="{{ $tglSelesai }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Kategori Kendaraan</label>
                <select name="kategori_kendaraan_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary flex-fill">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
                <a href="{{ route('owner.rekap.print', ['tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai, 'kategori_kendaraan_id' => $kategoriId]) }}"
                    target="_blank" class="btn btn-success flex-fill">
                    <i class="fa-solid fa-print me-1"></i> Cetak
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Rekap Transaksi --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-table text-primary me-2"></i> Rekap Transaksi Parkir</span>
        <small class="text-muted">Periode: {{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>Kode Tiket</th>
                        <th>Plat Nomor</th>
                        <th>Kategori</th>
                        <th>Area</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Durasi</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiList as $t)
                        <tr>
                            <td><code class="fw-bold text-primary">{{ $t->kode_tiket }}</code></td>
                            <td class="fw-bold">{{ $t->kendaraan->plat_nomor ?? $t->plat_nomor }}</td>
                            <td>{{ $t->kendaraan->kategoriKendaraan->nama_kategori ?? $t->kategoriKendaraan->nama_kategori ?? '-' }}</td>
                            <td>{{ $t->areaParkir->nama_area ?? '-' }}</td>
                            <td>{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($t->waktu_keluar)
                                    {{ $t->waktu_keluar->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td>
                                @if($t->durasi_jam > 0)
                                    {{ $t->durasi_jam }} jam
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">
                                @if($t->total_bayar > 0)
                                    Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Parkir</span>
                                @endif
                            </td>
                            <td>{{ $t->petugasMasuk->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Tidak ada data transaksi pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3 border-top">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-bold text-dark">{{ $transaksiList->firstItem() ?? 0 }}</span> s/d <span class="fw-bold text-dark">{{ $transaksiList->lastItem() ?? 0 }}</span> dari total <span class="fw-bold text-dark">{{ $transaksiList->total() }}</span> transaksi
            </div>
            <div>
                {{ $transaksiList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection

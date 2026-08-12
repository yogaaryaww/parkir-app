@extends('layouts.app')

@section('title', 'Transaksi Kendaraan Keluar')

@section('content')
<div class="row g-4">
    <!-- Form Pencarian Tiket / Scan Barcode -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="fa-solid fa-barcode me-2"></i> Cari Tiket / Scan Barcode
            </div>
            <div class="card-body">
                <form action="{{ route('petugas.transaksi.keluar') }}" method="GET">
                    <div class="mb-3">
                        <label for="q" class="form-label fw-bold">Kode Tiket atau Plat Nomor</label>
                        <div class="input-group input-group-lg">
                            <input type="text" name="q" id="q" class="form-control fw-bold text-uppercase" placeholder="PRK-2026... / B 1234 CD" value="{{ $q }}" required autofocus>
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                            </button>
                        </div>
                        <div class="form-text">Masukkan kode tiket atau nomor polisi kendaraan.</div>
                    </div>
                </form>
            </div>
        </div>

        @if($transaksi && $kalkulasi)
        <!-- Box Kalkulasi Tagihan & Pembayaran -->
        <div class="card border-primary">
            <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-calculator me-2"></i> rincian Pembayaran</span>
                <span class="badge bg-light text-primary fs-6">{{ $transaksi->kode_tiket }}</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-3" style="font-size: 0.95rem;">
                    <tr>
                        <td class="text-muted">Plat Nomor:</td>
                        <td class="fw-bold fs-5 text-dark">{{ $transaksi->plat_nomor }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kendaraan:</td>
                        <td class="fw-semibold">{{ $transaksi->kategoriKendaraan ? $transaksi->kategoriKendaraan->nama_kategori : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu Masuk:</td>
                        <td>{{ $transaksi->waktu_masuk->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu Keluar:</td>
                        <td>{{ $kalkulasi['waktu_keluar']->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Durasi Parkir:</td>
                        <td><span class="badge bg-warning text-dark fs-6">{{ $kalkulasi['durasi_jam'] }} Jam</span></td>
                    </tr>
                </table>

                <div class="p-3 bg-light rounded mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Total Tagihan Parkir:</span>
                        <span class="fw-bold text-danger fs-3" id="displayTotal">Rp {{ number_format($kalkulasi['total_bayar'], 0, ',', '.') }}</span>
                    </div>
                    <div class="small text-muted text-end">
                        (Jam 1: Rp {{ number_format($kalkulasi['tarif_jam_pertama'], 0, ',', '.') }}
                        @if($kalkulasi['durasi_jam'] > 1)
                            + {{ $kalkulasi['durasi_jam'] - 1 }} jam x Rp {{ number_format($kalkulasi['tarif_jam_berikutnya'], 0, ',', '.') }}
                        @endif)
                    </div>
                </div>

                <form action="{{ route('petugas.transaksi.keluar.process', $transaksi->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="bayar" class="form-label fw-bold">Uang Diterima (Rp)</label>
                        <input type="number" name="bayar" id="bayar" class="form-control form-control-lg fw-bold text-success fs-4 @error('bayar') is-invalid @enderror" placeholder="0" required min="{{ $kalkulasi['total_bayar'] }}">
                        @error('bayar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="p-3 bg-success bg-opacity-10 rounded mb-4 border border-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success">Uang Kembalian:</span>
                            <span class="fw-bold text-success fs-4" id="displayKembalian">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold" id="btnSelesai">
                        <i class="fa-solid fa-circle-check me-2"></i> Selesaikan & Cetak Struk
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Data Kendaraan Aktif -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-list text-primary me-2"></i> Daftar Kendaraan Sedang Parkir</span>
                <span class="badge bg-secondary">{{ count($kendaraanAktif) }} Kendaraan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Tiket</th>
                                <th>Plat Nomor</th>
                                <th>Jenis</th>
                                <th>Area</th>
                                <th>Jam Masuk</th>
                                <th class="text-center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kendaraanAktif as $item)
                                <tr class="{{ isset($transaksi) && $transaksi->id === $item->id ? 'table-primary' : '' }}">
                                    <td><code class="fw-bold text-primary">{{ $item->kode_tiket }}</code></td>
                                    <td class="fw-bold text-dark">{{ $item->plat_nomor }}</td>
                                    <td>{{ $item->kategoriKendaraan ? $item->kategoriKendaraan->nama_kategori : '-' }}</td>
                                    <td>{{ $item->areaParkir ? $item->areaParkir->nama_area : '-' }}</td>
                                    <td class="text-muted">{{ $item->waktu_masuk->format('H:i:s') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('petugas.transaksi.keluar', ['q' => $item->kode_tiket]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Tidak ada kendaraan aktif di area parkir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if($transaksi && $kalkulasi)
<script>
    const totalBayar = {{ $kalkulasi['total_bayar'] }};
    const inputBayar = document.getElementById('bayar');
    const displayKembalian = document.getElementById('displayKembalian');

    inputBayar.addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        const kembalian = val - totalBayar;
        if (kembalian >= 0) {
            displayKembalian.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
        } else {
            displayKembalian.innerText = 'Rp 0 (Kurang)';
        }
    });
</script>
@endif
@endpush
@endsection

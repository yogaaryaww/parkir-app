@extends('layouts.app')

@section('title', 'Transaksi Kendaraan Masuk')

@section('content')
<div class="row g-4">
    <!-- Form Kendaraan Masuk -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Input Kendaraan Masuk
            </div>
            <div class="card-body">
                <form action="{{ route('petugas.transaksi.masuk.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="plat_nomor" class="form-label fw-bold">Nomor Polisi / Plat Nomor</label>
                        <input type="text" name="plat_nomor" id="plat_nomor" class="form-control form-control-lg text-uppercase fw-bold @error('plat_nomor') is-invalid @enderror" value="{{ old('plat_nomor') }}" required placeholder="Contoh: B 1234 CD" autofocus style="letter-spacing: 0.1em;">
                        @error('plat_nomor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kategori_kendaraan_id" class="form-label fw-bold">Jenis / Kategori Kendaraan</label>
                        <select name="kategori_kendaraan_id" id="kategori_kendaraan_id" class="form-select form-select-lg @error('kategori_kendaraan_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Jenis Kendaraan --</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_kendaraan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }} (Rp {{ number_format($k->tarif->tarif_jam_pertama ?? 0, 0, ',', '.') }}/jam 1)
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_kendaraan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="area_parkir_id" class="form-label fw-bold">Lokasi / Area Parkir</label>
                        <select name="area_parkir_id" id="area_parkir_id" class="form-select form-select-lg @error('area_parkir_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Area Parkir --</option>
                            @foreach($areaList as $a)
                                @php $sisa = $a->kapasitas - $a->terisi; @endphp
                                <option value="{{ $a->id }}" {{ old('area_parkir_id') == $a->id ? 'selected' : '' }} {{ $sisa <= 0 ? 'disabled' : '' }}>
                                    {{ $a->nama_area }} (Tersedia: {{ $sisa }}/{{ $a->kapasitas }} Slot) {{ $sisa <= 0 ? '- [PENUH]' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_parkir_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                        <i class="fa-solid fa-print me-2"></i> Simpan & Cetak Struk Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Kendaraan Aktif di Area Parkir -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-car text-primary me-2"></i> Kendaraan Aktif di Area Parkir</span>
                <span class="badge bg-primary">{{ count($kendaraanAktif) }} Terbaca</span>
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
                                <th>Struk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kendaraanAktif as $t)
                                <tr>
                                    <td><code class="fw-bold text-primary">{{ $t->kode_tiket }}</code></td>
                                    <td class="fw-bold text-dark">{{ $t->plat_nomor }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $t->kategoriKendaraan ? $t->kategoriKendaraan->nama_kategori : '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $t->areaParkir ? $t->areaParkir->nama_area : '-' }}</td>
                                    <td class="text-muted">{{ $t->waktu_masuk->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('petugas.struk.masuk', $t->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Ulang Struk">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada kendaraan di area parkir.</td>
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

@extends('layouts.app')

@section('title', 'Transaksi Kendaraan Masuk')

@section('content')
<div class="row g-4">
    <!-- Form Kendaraan Masuk -->
    <div class="col-lg-5">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-right-to-bracket me-2"></i> Input Kendaraan Masuk</span>
                <span class="badge bg-white text-primary fw-semibold"><i class="fa-solid fa-shield-check me-1"></i> Auto-Register</span>
            </div>
            <div class="card-body">
                <form action="{{ route('petugas.transaksi.masuk.store') }}" method="POST" id="formTransaksiMasuk">
                    @csrf

                    {{-- 1. Input Plat Nomor --}}
                    <div class="mb-3">
                        <label for="plat_nomor" class="form-label fw-bold">Nomor Polisi / Plat Nomor</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-primary"><i class="fa-solid fa-id-card"></i></span>
                            <input type="text" name="plat_nomor" id="plat_nomor" list="master_kendaraan"
                                class="form-control text-uppercase fw-bold @error('plat_nomor') is-invalid @enderror"
                                value="{{ old('plat_nomor') }}" required placeholder="Contoh: B 1234 ABC" autofocus style="letter-spacing: 0.08em;"
                                autocomplete="off">
                        </div>
                        
                        <datalist id="master_kendaraan">
                            @foreach($kendaraanList as $kd)
                                <option value="{{ $kd->plat_nomor }}">
                                    {{ $kd->nama_pemilik }} ({{ $kd->kategoriKendaraan->nama_kategori ?? '-' }})
                                </option>
                            @endforeach
                        </datalist>
                        
                        @error('plat_nomor') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="form-text small text-muted">
                            Ketik plat nomor. Jika belum ada di master data, Anda dapat langsung mendaftarkannya di bawah ini.
                        </div>
                    </div>

                    {{-- 2. Box Status Kendaraan Ditemukan --}}
                    <div id="info_kendaraan_ada" class="alert alert-success py-2 px-3 mb-3 d-none border-success">
                        <div class="d-flex align-items-center mb-1 text-success fw-bold">
                            <i class="fa-solid fa-circle-check me-2"></i> Kendaraan Terdaftar di Master Data
                        </div>
                        <div class="d-flex justify-content-between align-items-center small mt-1">
                            <span class="text-muted">Pemilik:</span>
                            <span class="fw-bold text-dark" id="info_pemilik">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small mt-1">
                            <span class="text-muted">Jenis Kendaraan:</span>
                            <span class="badge bg-success" id="info_kategori">-</span>
                        </div>
                    </div>

                    {{-- 3. Box & Form Kendaraan Baru (On-The-Fly) --}}
                    <div id="section_kendaraan_baru" class="p-3 bg-light rounded border border-warning mb-3 {{ old('nama_pemilik') || old('kategori_kendaraan_id') ? '' : 'd-none' }}">
                        <div class="d-flex align-items-center text-warning-emphasis fw-bold mb-2 pb-1 border-bottom border-warning">
                            <i class="fa-solid fa-triangle-exclamation text-warning me-2 fs-5"></i>
                            <span>Kendaraan Baru (Belum Terdaftar)</span>
                        </div>
                        <p class="small text-muted mb-3">
                            Lengkapi identitas kendaraan berikut untuk mendaftarkan dan membuat tiket parkir secara instan:
                        </p>

                        <div class="mb-2">
                            <label for="kategori_kendaraan_id" class="form-label small fw-bold">Kategori / Jenis Kendaraan <span class="text-danger">*</span></label>
                            <select name="kategori_kendaraan_id" id="kategori_kendaraan_id" class="form-select @error('kategori_kendaraan_id') is-invalid @enderror">
                                <option value="" disabled {{ old('kategori_kendaraan_id') ? '' : 'selected' }}>-- Pilih Kategori Kendaraan --</option>
                                @foreach($kategoriList as $k)
                                    <option value="{{ $k->id }}" {{ old('kategori_kendaraan_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }} (Rp {{ number_format($k->tarif->tarif_jam_pertama ?? 0, 0, ',', '.') }}/jam 1)
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_kendaraan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-2">
                            <label for="nama_pemilik" class="form-label small fw-bold">Nama Pemilik <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control @error('nama_pemilik') is-invalid @enderror"
                                value="{{ old('nama_pemilik') }}" placeholder="Contoh: Tamu / Andi / Nama Pemilik">
                            @error('nama_pemilik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-1">
                            <label for="keterangan" class="form-label small fw-semibold text-muted">Keterangan / Merek / Warna <span class="fw-normal">(Opsional)</span></label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control form-control-sm @error('keterangan') is-invalid @enderror"
                                value="{{ old('keterangan') }}" placeholder="Contoh: Honda Vario Hitam">
                            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- 4. Pilihan Lokasi Area Parkir --}}
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

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" id="btnSubmitMasuk">
                        <i class="fa-solid fa-print me-2"></i> Simpan & Cetak Struk Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Kendaraan Aktif di Area Parkir -->
    <div class="col-lg-7">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold"><i class="fa-solid fa-car text-primary me-2"></i> Kendaraan Aktif di Area Parkir</span>
                <span class="badge bg-primary px-3 py-2 fs-6">{{ count($kendaraanAktif) }} Kendaraan</span>
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
                                    <td class="fw-bold text-dark">
                                        {{ $t->kendaraan->plat_nomor ?? $t->plat_nomor }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $t->kendaraan->kategoriKendaraan->nama_kategori ?? $t->kategoriKendaraan->nama_kategori ?? '-' }}
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

@push('scripts')
<script>
    const dataKendaraan = {
        @foreach($kendaraanList as $k)
            "{{ strtoupper(trim($k->plat_nomor)) }}": {
                pemilik: "{{ addslashes($k->nama_pemilik) }}",
                kategori: "{{ addslashes($k->kategoriKendaraan->nama_kategori ?? '-') }}"
            },
        @endforeach
    };

    const platInput = document.getElementById('plat_nomor');
    const infoAda = document.getElementById('info_kendaraan_ada');
    const infoPemilik = document.getElementById('info_pemilik');
    const infoKategori = document.getElementById('info_kategori');
    const sectionBaru = document.getElementById('section_kendaraan_baru');
    const inputKategori = document.getElementById('kategori_kendaraan_id');
    const inputPemilik = document.getElementById('nama_pemilik');
    const btnSubmit = document.getElementById('btnSubmitMasuk');

    function checkPlat() {
        const val = platInput.value.toUpperCase().trim();

        if (!val) {
            infoAda.classList.add('d-none');
            sectionBaru.classList.add('d-none');
            inputKategori.removeAttribute('required');
            inputPemilik.removeAttribute('required');
            btnSubmit.innerHTML = '<i class="fa-solid fa-print me-2"></i> Simpan & Cetak Struk Masuk';
            return;
        }

        if (dataKendaraan[val]) {
            // Kendaraan Ditemukan di Master Data
            infoPemilik.innerText = dataKendaraan[val].pemilik;
            infoKategori.innerText = dataKendaraan[val].kategori;
            infoAda.classList.remove('d-none');
            sectionBaru.classList.add('d-none');
            inputKategori.removeAttribute('required');
            inputPemilik.removeAttribute('required');
            btnSubmit.innerHTML = '<i class="fa-solid fa-print me-2"></i> Simpan & Cetak Struk Masuk';
        } else {
            // Kendaraan Belum Terdaftar -> Tampilkan form pendaftaran
            infoAda.classList.add('d-none');
            sectionBaru.classList.remove('d-none');
            inputKategori.setAttribute('required', 'required');
            inputPemilik.setAttribute('required', 'required');
            btnSubmit.innerHTML = '<i class="fa-solid fa-plus-circle me-2"></i> Daftarkan Kendaraan & Cetak Struk';
        }
    }

    platInput.addEventListener('input', checkPlat);
    platInput.addEventListener('change', checkPlat);
    checkPlat();
</script>
@endpush
@endsection


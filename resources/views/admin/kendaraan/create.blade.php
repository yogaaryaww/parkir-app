@extends('layouts.app')

@section('title', 'Tambah Data Kendaraan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-plus text-primary me-2"></i> Tambah Data Kendaraan Baru</span>
                <a href="{{ route('admin.kendaraan.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.kendaraan.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="plat_nomor" class="form-label fw-semibold">Nomor Polisi (Plat Nomor)</label>
                        <input type="text" name="plat_nomor" id="plat_nomor"
                            class="form-control text-uppercase fw-bold @error('plat_nomor') is-invalid @enderror"
                            value="{{ old('plat_nomor') }}"
                            required
                            placeholder="Contoh: B 1234 ABC"
                            style="letter-spacing: 0.05em;">
                        @error('plat_nomor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Plat nomor harus unik. Contoh format: B 1234 ABC</div>
                    </div>

                    <div class="mb-3">
                        <label for="kategori_kendaraan_id" class="form-label fw-semibold">Kategori Kendaraan</label>
                        <select name="kategori_kendaraan_id" id="kategori_kendaraan_id"
                            class="form-select @error('kategori_kendaraan_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_kendaraan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_kendaraan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama_pemilik" class="form-label fw-semibold">Nama Pemilik</label>
                        <input type="text" name="nama_pemilik" id="nama_pemilik"
                            class="form-control @error('nama_pemilik') is-invalid @enderror"
                            value="{{ old('nama_pemilik') }}"
                            required
                            placeholder="Nama lengkap pemilik kendaraan">
                        @error('nama_pemilik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">Keterangan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Deskripsi kendaraan, warna, merek, dll.">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <button type="reset" class="btn btn-light">Reset</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Simpan Kendaraan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

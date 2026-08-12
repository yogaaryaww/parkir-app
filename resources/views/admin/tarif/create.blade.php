@extends('layouts.app')

@section('title', 'Tambah Tarif Parkir')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-plus text-primary me-2"></i> Tambah Tarif Parkir</span>
                <a href="{{ route('admin.tarif.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if($kategoriList->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Semua kategori kendaraan yang tersedia telah memiliki aturan tarif parkir. Silakan edit tarif yang sudah ada atau buat kategori baru terlebih dahulu.
                    </div>
                @else
                <form action="{{ route('admin.tarif.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="kategori_kendaraan_id" class="form-label fw-semibold">Pilih Kategori Kendaraan</label>
                        <select name="kategori_kendaraan_id" id="kategori_kendaraan_id" class="form-select @error('kategori_kendaraan_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_kendaraan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tarif_jam_pertama" class="form-label fw-semibold">Tarif Jam Pertama (Rp)</label>
                        <input type="number" name="tarif_jam_pertama" id="tarif_jam_pertama" class="form-control @error('tarif_jam_pertama') is-invalid @enderror" value="{{ old('tarif_jam_pertama') }}" required min="0" placeholder="Contoh: 3000">
                        @error('tarif_jam_pertama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tarif_jam_berikutnya" class="form-label fw-semibold">Tarif Jam Berikutnya (Rp/Jam)</label>
                        <input type="number" name="tarif_jam_berikutnya" id="tarif_jam_berikutnya" class="form-control @error('tarif_jam_berikutnya') is-invalid @enderror" value="{{ old('tarif_jam_berikutnya') }}" required min="0" placeholder="Contoh: 2000">
                        @error('tarif_jam_berikutnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="tarif_maksimal" class="form-label fw-semibold">Tarif Maksimal Per Hari (Rp) <span class="text-muted small fw-normal">(Isi 0 jika tanpa batas)</span></label>
                        <input type="number" name="tarif_maksimal" id="tarif_maksimal" class="form-control @error('tarif_maksimal') is-invalid @enderror" value="{{ old('tarif_maksimal', 0) }}" min="0" placeholder="Contoh: 20000">
                        @error('tarif_maksimal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Simpan Tarif</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

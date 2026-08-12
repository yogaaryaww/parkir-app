@extends('layouts.app')

@section('title', 'Edit Tarif Parkir')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Tarif Parkir</span>
                <a href="{{ route('admin.tarif.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tarif.update', $tarif->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Kendaraan</label>
                        <input type="text" class="form-control bg-light" value="{{ $tarif->kategoriKendaraan ? $tarif->kategoriKendaraan->nama_kategori : '-' }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="tarif_jam_pertama" class="form-label fw-semibold">Tarif Jam Pertama (Rp)</label>
                        <input type="number" name="tarif_jam_pertama" id="tarif_jam_pertama" class="form-control @error('tarif_jam_pertama') is-invalid @enderror" value="{{ old('tarif_jam_pertama', $tarif->tarif_jam_pertama) }}" required min="0">
                        @error('tarif_jam_pertama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tarif_jam_berikutnya" class="form-label fw-semibold">Tarif Jam Berikutnya (Rp/Jam)</label>
                        <input type="number" name="tarif_jam_berikutnya" id="tarif_jam_berikutnya" class="form-control @error('tarif_jam_berikutnya') is-invalid @enderror" value="{{ old('tarif_jam_berikutnya', $tarif->tarif_jam_berikutnya) }}" required min="0">
                        @error('tarif_jam_berikutnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="tarif_maksimal" class="form-label fw-semibold">Tarif Maksimal Per Hari (Rp)</label>
                        <input type="number" name="tarif_maksimal" id="tarif_maksimal" class="form-control @error('tarif_maksimal') is-invalid @enderror" value="{{ old('tarif_maksimal', $tarif->tarif_maksimal) }}" min="0">
                        @error('tarif_maksimal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Perbarui Tarif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

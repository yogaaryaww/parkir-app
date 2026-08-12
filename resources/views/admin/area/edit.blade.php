@extends('layouts.app')

@section('title', 'Edit Area Parkir')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Area Parkir</span>
                <a href="{{ route('admin.area.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.area.update', $area->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama_area" class="form-label fw-semibold">Nama Area Parkir</label>
                        <input type="text" name="nama_area" id="nama_area" class="form-control @error('nama_area') is-invalid @enderror" value="{{ old('nama_area', $area->nama_area) }}" required>
                        @error('nama_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kapasitas" class="form-label fw-semibold">Kapasitas Slot Kendaraan</label>
                        <input type="number" name="kapasitas" id="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" value="{{ old('kapasitas', $area->kapasitas) }}" required min="{{ $area->terisi }}">
                        <div class="form-text text-muted">Jumlah terisi saat ini: {{ $area->terisi }} slot.</div>
                        @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Perbarui Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Kategori Kendaraan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Kategori Kendaraan</span>
                <a href="{{ route('admin.kategori.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.kategori.update', $kategori->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                        @error('nama_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $kategori->keterangan) }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Perbarui Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

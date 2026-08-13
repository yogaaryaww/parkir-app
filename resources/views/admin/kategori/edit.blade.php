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

                    @php $currentIcon = old('icon_class', $kategori->icon_class ?: 'fa-solid fa-car'); @endphp
                    <div class="mb-3">
                        <label for="icon_select" class="form-label fw-semibold">Pilih Ikon Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light" id="iconPreviewContainer">
                                <i id="iconPreview" class="{{ $currentIcon }} text-primary fs-5"></i>
                            </span>
                            <select id="icon_select" class="form-select" onchange="updateIconInput(this.value)">
                                <option value="fa-solid fa-motorcycle" {{ $currentIcon == 'fa-solid fa-motorcycle' ? 'selected' : '' }}>Motor (Sepeda Motor)</option>
                                <option value="fa-solid fa-car" {{ $currentIcon == 'fa-solid fa-car' ? 'selected' : '' }}>Mobil (Sedan/SUV/Minibus)</option>
                                <option value="fa-solid fa-bolt-lightning" {{ $currentIcon == 'fa-solid fa-bolt-lightning' ? 'selected' : '' }}>Motor Listrik / EV (Petir / Listrik)</option>
                                <option value="bi bi-lightning-charge" {{ $currentIcon == 'bi bi-lightning-charge' ? 'selected' : '' }}>Kendaraan Listrik (BI Lightning)</option>
                                <option value="fa-solid fa-truck" {{ $currentIcon == 'fa-solid fa-truck' ? 'selected' : '' }}>Truk / Logistik</option>
                                <option value="fa-solid fa-bus" {{ $currentIcon == 'fa-solid fa-bus' ? 'selected' : '' }}>Bus / Microbus</option>
                                <option value="bi bi-bicycle" {{ $currentIcon == 'bi bi-bicycle' ? 'selected' : '' }}>Sepeda Kayuh</option>
                                <option value="fa-solid fa-shield-halved" {{ $currentIcon == 'fa-solid fa-shield-halved' ? 'selected' : '' }}>Generik / Lainnya</option>
                                <option value="custom" {{ !in_array($currentIcon, ['fa-solid fa-motorcycle','fa-solid fa-car','fa-solid fa-bolt-lightning','bi bi-lightning-charge','fa-solid fa-truck','fa-solid fa-bus','bi bi-bicycle','fa-solid fa-shield-halved']) ? 'selected' : '' }}>Kustom (Ketik Manual)...</option>
                            </select>
                        </div>
                        <input type="text" name="icon_class" id="icon_class" class="form-control mt-2 @error('icon_class') is-invalid @enderror" value="{{ $currentIcon }}" placeholder="Contoh: fa-solid fa-bolt-lightning">
                        <div class="form-text small">Pilih dari preset atau ketik nama icon class (FontAwesome / Bootstrap Icons).</div>
                        @error('icon_class') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

@push('scripts')
<script>
    function updateIconInput(val) {
        const iconInput = document.getElementById('icon_class');
        const preview = document.getElementById('iconPreview');
        if (val !== 'custom') {
            iconInput.value = val;
        }
        preview.className = iconInput.value + ' text-primary fs-5';
    }

    document.getElementById('icon_class').addEventListener('input', function() {
        document.getElementById('iconPreview').className = this.value + ' text-primary fs-5';
    });
</script>
@endpush
@endsection

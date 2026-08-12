@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold"><i class="fa-solid fa-car text-primary me-2"></i> Master Data Kendaraan Terdaftar</span>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.kendaraan.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari plat / pemilik..." value="{{ request('q') }}" style="min-width: 200px;">
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
                @if(request('q'))
                    <a href="{{ route('admin.kendaraan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </form>
            <a href="{{ route('admin.kendaraan.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kendaraan
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Plat Nomor</th>
                        <th>Nama Pemilik</th>
                        <th>Kategori Kendaraan</th>
                        <th>Keterangan</th>
                        <th>Terdaftar</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kendaraan as $k)
                        <tr>
                            <td>{{ $kendaraan->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="badge bg-dark fs-6 fw-bold" style="letter-spacing: 0.05em;">{{ $k->plat_nomor }}</span>
                            </td>
                            <td class="fw-semibold">{{ $k->nama_pemilik }}</td>
                            <td>
                                @if($k->kategoriKendaraan)
                                    <span class="badge bg-primary">{{ $k->kategoriKendaraan->nama_kategori }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $k->keterangan ?? '-' }}</td>
                            <td class="text-muted small">{{ $k->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.kendaraan.edit', $k->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.kendaraan.destroy', $k->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus kendaraan {{ $k->plat_nomor }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                @if(request('q'))
                                    Tidak ditemukan kendaraan dengan kata kunci "{{ request('q') }}".
                                @else
                                    Belum ada data kendaraan terdaftar.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($kendaraan->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $kendaraan->links() }}
        </div>
    @endif
</div>
@endsection

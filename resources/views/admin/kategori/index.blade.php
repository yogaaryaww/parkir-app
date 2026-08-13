@extends('layouts.app')

@section('title', 'Kategori Kendaraan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-car-side text-primary me-2"></i> Master Data Kategori Kendaraan</span>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th>Tarif Jam Pertama</th>
                        <th>Tarif Jam Berikutnya</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold text-primary">
                                <i class="{{ $k->icon_class ?: 'fa-solid fa-car' }} me-2"></i>
                                {{ $k->nama_kategori }}
                            </td>
                            <td class="text-muted">{{ $k->keterangan ?? '-' }}</td>
                            <td>
                                @if($k->tarif)
                                    <span class="fw-semibold">Rp {{ number_format($k->tarif->tarif_jam_pertama, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum diset</span>
                                @endif
                            </td>
                            <td>
                                @if($k->tarif)
                                    <span class="fw-semibold">Rp {{ number_format($k->tarif->tarif_jam_berikutnya, 0, ',', '.') }} / jam</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum diset</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.kategori.edit', $k->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.kategori.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Menghapus kategori akan menghapus tarif terkait. Lanjutkan?')">
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
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data kategori kendaraan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tarif Parkir')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-tags text-primary me-2"></i> Aturan Tarif Parkir Kendaraan</span>
        <a href="{{ route('admin.tarif.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Tarif Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Kategori Kendaraan</th>
                        <th>Tarif Jam 1</th>
                        <th>Tarif Jam Berikutnya</th>
                        <th>Tarif Maksimal (24 Jam)</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarif as $index => $t)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">
                                {{ $t->kategoriKendaraan ? $t->kategoriKendaraan->nama_kategori : 'Kategori Terhapus' }}
                            </td>
                            <td class="text-success fw-bold">Rp {{ number_format($t->tarif_jam_pertama, 0, ',', '.') }}</td>
                            <td class="text-primary fw-bold">Rp {{ number_format($t->tarif_jam_berikutnya, 0, ',', '.') }} / jam</td>
                            <td>
                                @if($t->tarif_maksimal > 0)
                                    <span class="badge bg-info text-dark">Rp {{ number_format($t->tarif_maksimal, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-secondary">Tanpa Batas</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.tarif.edit', $t->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.tarif.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan tarif ini?')">
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
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data aturan tarif parkir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

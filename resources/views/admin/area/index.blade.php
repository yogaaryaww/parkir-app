@extends('layouts.app')

@section('title', 'Area Parkir')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-layer-group text-primary me-2"></i> Data Area / Lokasi Parkir</span>
        <a href="{{ route('admin.area.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Area Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Area</th>
                        <th>Kapasitas</th>
                        <th>Terisi</th>
                        <th>Sisa Slot</th>
                        <th>Status Status Status Status Status Status Status Status Status Status Status Status Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($area as $index => $a)
                        @php
                            $sisa = $a->kapasitas - $a->terisi;
                            $persen = $a->kapasitas > 0 ? round(($a->terisi / $a->kapasitas) * 100) : 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $a->nama_area }}</td>
                            <td><span class="badge bg-secondary fs-6">{{ $a->kapasitas }} Slot</span></td>
                            <td><span class="badge bg-primary fs-6">{{ $a->terisi }}</span></td>
                            <td><span class="badge bg-success fs-6">{{ $sisa }} Slot</span></td>
                            <td style="min-width: 150px;">
                                <div class="progress" style="height: 14px;">
                                    <div class="progress-bar 
                                        @if($persen >= 90) bg-danger 
                                        @elseif($persen >= 75) bg-warning 
                                        @else bg-success 
                                        @endif" 
                                        role="progressbar" style="width: {{ $persen }}%">
                                        {{ $persen }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.area.edit', $a->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.area.destroy', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus area parkir ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" {{ $a->terisi > 0 ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data area parkir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

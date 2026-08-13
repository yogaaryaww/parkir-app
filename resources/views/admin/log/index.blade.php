@extends('layouts.app')

@section('title', 'Log Aktivitas System')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
        <span class="fw-bold fs-6"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Audit Log Aktivitas Pengguna</span>
        
        <!-- Form Search -->
        <form action="{{ route('admin.log.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 280px;">
                <input type="text" name="q" class="form-control" placeholder="Cari aktivitas / user / deskripsi..." value="{{ request('q') }}">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass me-1"></i> Cari</button>
                @if(request('q'))
                    <a href="{{ route('admin.log.index') }}" class="btn btn-outline-secondary" title="Reset Search"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th width="160">Waktu Catat</th>
                        <th width="180">User</th>
                        <th width="180">Aktivitas</th>
                        <th>Deskripsi</th>
                        <th width="140">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $logs->firstItem() + $index }}</td>
                            <td class="text-secondary" style="white-space: nowrap; font-size: 0.85rem;">
                                <i class="fa-regular fa-clock me-1 text-muted"></i>{{ $log->created_at->format('d-m-Y H:i:s') }}
                            </td>
                            <td>
                                @if($log->user)
                                    <span class="fw-bold text-dark">{{ $log->user->nama }}</span>
                                    <span class="badge 
                                        @if($log->user->role === 'admin') bg-danger 
                                        @elseif($log->user->role === 'petugas') bg-primary 
                                        @else bg-success 
                                        @endif ms-1" style="font-size: 0.65rem;">
                                        {{ strtoupper($log->user->role) }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">System / Unknown</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if(str_contains(strtolower($log->aktivitas), 'login')) bg-info text-dark
                                    @elseif(str_contains(strtolower($log->aktivitas), 'tambah')) bg-success
                                    @elseif(str_contains(strtolower($log->aktivitas), 'hapus')) bg-danger
                                    @elseif(str_contains(strtolower($log->aktivitas), 'logout')) bg-secondary
                                    @else bg-primary
                                    @endif" style="font-size: 0.78rem;">
                                    {{ $log->aktivitas }}
                                </span>
                            </td>
                            <td class="text-dark">{{ $log->deskripsi ?? '-' }}</td>
                            <td><code class="px-2 py-1 bg-light rounded text-dark" style="font-size: 0.8rem;">{{ $log->ip_address ?? '127.0.0.1' }}</code></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fa-solid fa-inbox fa-2x mb-2 text-black-50 d-block"></i>
                                Belum ada riwayat aktivitas log yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-white py-3 border-top">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-bold text-dark">{{ $logs->firstItem() ?? 0 }}</span> s/d <span class="fw-bold text-dark">{{ $logs->lastItem() ?? 0 }}</span> dari total <span class="fw-bold text-dark">{{ $logs->total() }}</span> log aktivitas
            </div>
            <div>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling khusus pagination Bootstrap 5 agar rapi */
    .card-footer .pagination {
        margin: 0;
        gap: 2px;
    }
    .card-footer .pagination .page-item .page-link {
        border-radius: 0.375rem;
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .card-footer .pagination .page-item.active .page-link {
        background-color: #0284c7;
        border-color: #0284c7;
        color: #ffffff;
        font-weight: 600;
    }
    .card-footer .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: #f8fafc;
    }
</style>
@endsection

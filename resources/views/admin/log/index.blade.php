@extends('layouts.app')

@section('title', 'Log Aktivitas System')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Audit Log Aktivitas Pengguna</span>
        
        <!-- Form Search -->
        <form action="{{ route('admin.log.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari aktivitas / user..." value="{{ request('q') }}">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
            @if(request('q'))
                <a href="{{ route('admin.log.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset"><i class="fa-solid fa-xmark"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Waktu Catat</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td class="text-muted" style="white-space: nowrap;">
                                {{ $log->created_at->format('d-m-Y H:i:s') }}
                            </td>
                            <td>
                                @if($log->user)
                                    <span class="fw-bold text-dark">{{ $log->user->nama }}</span>
                                    <span class="badge bg-light text-dark border ms-1">{{ strtoupper($log->user->role) }}</span>
                                @else
                                    <span class="text-muted fst-italic">System / Unknown</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if(str_contains(strtolower($log->aktivitas), 'login')) bg-info text-dark
                                    @elseif(str_contains(strtolower($log->aktivitas), 'tambah')) bg-success
                                    @elseif(str_contains(strtolower($log->aktivitas), 'hapus')) bg-danger
                                    @else bg-primary
                                    @endif">
                                    {{ $log->aktivitas }}
                                </span>
                            </td>
                            <td class="text-dark">{{ $log->deskripsi ?? '-' }}</td>
                            <td><code>{{ $log->ip_address ?? '127.0.0.1' }}</code></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat aktivitas log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

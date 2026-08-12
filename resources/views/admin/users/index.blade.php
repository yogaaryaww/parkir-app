@extends('layouts.app')

@section('title', 'Kelola Data User')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-users text-primary me-2"></i> Daftar Pengguna Sistem</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah User Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $u->nama }}</td>
                            <td><code>{{ $u->username }}</code></td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <span class="badge 
                                    @if($u->role === 'admin') bg-danger 
                                    @elseif($u->role === 'petugas') bg-primary 
                                    @else bg-success 
                                    @endif">
                                    {{ strtoupper($u->role) }}
                                </span>
                            </td>
                            <td>
                                @if($u->status === 'aktif')
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-circle-xmark me-1"></i> Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

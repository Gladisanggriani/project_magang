@extends('layouts.main')

@section('title', 'Manajemen User')

@section('content')
<div class="page-card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
        <div>
            <h2 class="page-card-title">Manajemen User</h2>
            <p class="page-card-subtitle">
                Halaman ini digunakan oleh admin untuk mengatur role pengguna sistem.
            </p>
        </div>
    </div>
</div>

<div class="page-card">
    <div style="overflow-x:auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-badge 
                                @if($user->role === 'admin') status-danger
                                @elseif($user->role === 'operator') status-warning
                                @else status-neutral
                                @endif
                            ">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('d-m-Y') : '-' }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-primary">
                                    <i class="bi bi-pencil-square"></i> Ubah Role
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada user yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap" style="margin-top:18px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
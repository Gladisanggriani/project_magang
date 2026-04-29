@extends('layouts.main')

@section('title', 'Ubah Role User')

@section('content')
<div class="page-card">
    <h2 class="page-card-title">Ubah Role User</h2>
    <p class="page-card-subtitle">
        Atur hak akses pengguna berdasarkan kebutuhan sistem.
    </p>
</div>

@if($errors->any())
    <div class="alert-danger">
        <strong>Terjadi kesalahan:</strong>
        <ul style="margin:10px 0 0 18px;">
            @foreach($errors->all() as $error)
                <li style="margin-bottom:4px;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-section">
    <div class="form-section-title">
        <div class="form-section-icon">
            <i class="bi bi-person-gear"></i>
        </div>
        <span>Data User</span>
    </div>

    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td>Role Saat Ini</td>
            <td>{{ strtoupper($user->role) }}</td>
        </tr>
    </table>
</div>

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-section">
        <div class="form-section-title">
            <div class="form-section-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <span>Pilih Role Baru</span>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    <option value="operator" @selected(old('role', $user->role) === 'operator')>Operator</option>
                    <option value="viewer" @selected(old('role', $user->role) === 'viewer')>Viewer</option>
                </select>
            </div>
        </div>

        <div class="form-actions" style="margin-top:18px;">
            <button type="submit" class="btn-primary">
                <i class="bi bi-save"></i> Simpan Role
            </button>

            <a href="{{ route('users.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</form>
@endsection
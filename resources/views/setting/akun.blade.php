@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/setting.css') }}">
@endpush

@section('content')
<div class="settings-container">
    <div class="settings-grid" style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-user-cog"></i> Profil Akun Saya</h3>
            </div>
            
            <div class="card-body">
                <form action="{{ route('setting.akun.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="form-control" 
                               value="{{ old('nama', $user->nama) }}" placeholder="Nama Lengkap" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="{{ old('username', $user->username) }}" placeholder="Username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Kosongkan jika tidak diubah" minlength="6">
                        <small style="color: var(--text-tertiary); font-size: 11px; margin-top: 4px; display: block;">
                            Minimal 6 karakter. Hanya isi jika ingin mengganti password login Anda.
                        </small>
                    </div>
                    
                    <div style="margin-top: 24px; text-align: right;">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

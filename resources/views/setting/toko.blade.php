@extends('layouts.app')

@section('title', 'Informasi Toko')
@section('page-title', 'Informasi Toko')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/setting.css') }}">
@endpush

@section('content')
<div class="settings-container">
    <div class="settings-grid" style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-building-store"></i> Pengaturan Informasi Toko</h3>
            </div>
            
            <div class="card-body">
                <form action="{{ route('setting.toko.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="nama_toko">Nama Toko / Coffee Shop</label>
                        <input type="text" id="nama_toko" name="nama_toko" class="form-control" 
                               value="{{ old('nama_toko', $storeInfo['nama_toko']) }}" placeholder="Nama Toko" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="alamat">Alamat Toko</label>
                        <textarea id="alamat" name="alamat" class="form-control" rows="3" placeholder="Alamat Toko" required>{{ old('alamat', $storeInfo['alamat']) }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="telepon">Nomor Telepon</label>
                        <input type="text" id="telepon" name="telepon" class="form-control" 
                               value="{{ old('telepon', $storeInfo['telepon']) }}" placeholder="Nomor Telepon" required>
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

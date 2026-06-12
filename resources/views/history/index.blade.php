@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">
@endpush

@section('content')
<div class="history-container">
    <!-- Filter Card -->
    <div class="filter-card">
        <form action="{{ route('history') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label for="from">Dari Tanggal</label>
                <input type="date" id="from" name="from" class="filter-control" value="{{ $from }}">
            </div>
            
            <div class="filter-group">
                <label for="to">Sampai Tanggal</label>
                <input type="date" id="to" name="to" class="filter-control" value="{{ $to }}">
            </div>
            
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-filter"></i> Filter
                </button>
                @if($from || $to)
                    <a href="{{ route('history') }}" class="btn btn-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards-grid">
        <div class="summary-stat-card">
            <div class="summary-stat-icon">
                <i class="ti ti-cash"></i>
            </div>
            <div class="summary-stat-info">
                <span class="summary-stat-label">Total Pendapatan</span>
                <span class="summary-stat-value">{{ App\Helpers\FormatHelper::rupiah($totalPendapatan) }}</span>
            </div>
        </div>
        
        <div class="summary-stat-card">
            <div class="summary-stat-icon">
                <i class="ti ti-receipt"></i>
            </div>
            <div class="summary-stat-info">
                <span class="summary-stat-label">Jumlah Transaksi</span>
                <span class="summary-stat-value">{{ $jumlahTransaksi }} Transaksi</span>
            </div>
        </div>
    </div>

    <!-- Table of Transactions -->
    <div class="table-card">
        <div class="table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Kasir</th>
                        <th>Waktu</th>
                        <th>Total Pembayaran</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td class="trx-id-cell">{{ $trx->trx_id }}</td>
                            <td>{{ $trx->user->nama }}</td>
                            <td>{{ App\Helpers\FormatHelper::tanggalIndonesia($trx->tanggal) }}</td>
                            <td style="font-weight: 600;">{{ App\Helpers\FormatHelper::rupiah($trx->total_harga) }}</td>
                            <td>
                                <span class="badge badge-success">Lunas</span>
                            </td>
                            <td style="text-align: center;">
                                <button onclick="printReceipt('{{ route('history.print', $trx->trx_id) }}')" class="btn-print-action" title="Cetak Ulang Struk">
                                    <i class="ti ti-printer"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-tertiary); padding: 32px 20px;">
                                <i class="ti ti-notes-off" style="font-size: 36px; display: block; margin-bottom: 8px;"></i>
                                Tidak ditemukan riwayat transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function printReceipt(url) {
            window.open(url, '_blank', 'width=600,height=800');
        }
    </script>
@endpush

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk {{ $transaction->trx_id }}</title>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
</head>
<body>
    <!-- Receipt Body -->
    <div class="receipt-wrapper">
        <div class="receipt-header">
            <div class="store-name">{{ $storeInfo['nama_toko'] }}</div>
            <div class="store-info">
                {{ $storeInfo['alamat'] }}<br>
                Telp: {{ $storeInfo['telepon'] }}
            </div>
        </div>
        
        <div class="divider"></div>
        
        <table class="info-table">
            <tr>
                <td>No. Trx:</td>
                <td>{{ $transaction->trx_id }}</td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td>{{ $transaction->tanggal->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir:</td>
                <td>{{ $transaction->user->nama }}</td>
            </tr>
        </table>
        
        <div class="divider"></div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Item</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                    <th style="width: 25%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                    <tr>
                        <td>
                            {{ $detail->menu->nama_menu }}<br>
                            <span style="font-size: 9px; color: #555;">@ {{ App\Helpers\FormatHelper::rupiah($detail->harga) }}</span>
                        </td>
                        <td style="text-align: center;">{{ $detail->jumlah }}</td>
                        <td class="price-col">{{ App\Helpers\FormatHelper::rupiah($detail->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="divider"></div>
        
        <div class="totals-wrapper">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>{{ App\Helpers\FormatHelper::rupiah($subtotal) }}</span>
            </div>
            <div class="totals-row">
                <span>Pajak (10%):</span>
                <span>{{ App\Helpers\FormatHelper::rupiah($pajak) }}</span>
            </div>
            <div class="totals-row grand-total">
                <span>Total:</span>
                <span>{{ App\Helpers\FormatHelper::rupiah($transaction->total_harga) }}</span>
            </div>
            <div class="totals-row" style="margin-top: 5px;">
                <span>Bayar:</span>
                <span>{{ App\Helpers\FormatHelper::rupiah($transaction->uang_bayar) }}</span>
            </div>
            <div class="totals-row">
                <span>Kembali:</span>
                <span>{{ App\Helpers\FormatHelper::rupiah($transaction->kembalian) }}</span>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="receipt-footer">
            Terima Kasih Atas Kunjungan Anda<br>
            {{ $storeInfo['nama_toko'] }} - Rasa Kopi Terbaik
        </div>
    </div>

    <!-- Screen Action Control Buttons (Hidden when printing) -->
    <div class="no-print">
        <button onclick="window.print()" class="no-print-btn">Cetak Struk</button>
        <button onclick="window.close()" class="no-print-btn back-btn">Tutup</button>
    </div>

    <script>
        // Auto print on load
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Setting;
use App\Helpers\FormatHelper;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'details.menu'])
            ->orderBy('tanggal', 'desc');

        // Date filter
        $from = $request->get('from');
        $to = $request->get('to');

        if ($from && $to) {
            $query->whereBetween('tanggal', [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ]);
        } elseif ($from) {
            $query->where('tanggal', '>=', $from . ' 00:00:00');
        } elseif ($to) {
            $query->where('tanggal', '<=', $to . ' 23:59:59');
        }

        $transactions = $query->get();

        // Daily summary (aggregate query)
        $summaryQuery = Transaction::query();

        if ($from && $to) {
            $summaryQuery->whereBetween('tanggal', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } elseif ($from) {
            $summaryQuery->where('tanggal', '>=', $from . ' 00:00:00');
        } elseif ($to) {
            $summaryQuery->where('tanggal', '<=', $to . ' 23:59:59');
        } else {
            // Default: today
            $summaryQuery->whereDate('tanggal', today());
        }

        $totalPendapatan = $summaryQuery->sum('total_harga');
        $jumlahTransaksi = $summaryQuery->count();

        return view('history.index', [
            'transactions' => $transactions,
            'totalPendapatan' => $totalPendapatan,
            'jumlahTransaksi' => $jumlahTransaksi,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function print(string $trxId)
    {
        $transaction = Transaction::with(['user', 'details.menu'])
            ->where('trx_id', $trxId)
            ->firstOrFail();

        $storeInfo = Setting::getStoreInfo();

        // Calculate subtotal & pajak from details
        $subtotal = $transaction->details->sum('subtotal');
        $pajak = $transaction->total_harga - $subtotal;

        return view('history.print', [
            'transaction' => $transaction,
            'storeInfo' => $storeInfo,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
        ]);
    }
}

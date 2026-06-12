<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Generate a unique transaction ID.
     * Format: TRX-YYYYXXXXX (e.g., TRX-202500001)
     */
    public function generateTrxId(): string
    {
        $year = now()->year;
        $prefix = config('pos.trx_prefix') . $year;
        $seqLength = config('pos.trx_sequence_length');

        // Count existing transactions for this year + 1
        $count = Transaction::whereYear('tanggal', $year)->count() + 1;

        return $prefix . str_pad($count, $seqLength, '0', STR_PAD_LEFT);
    }

    /**
     * Save a transaction with all details atomically.
     * Reduces stock, creates transaction and detail records.
     */
    public function saveTransaction(array $cartItems, float $subtotal, float $pajak, float $total, float $uangBayar, int $userId): Transaction
    {
        return DB::transaction(function () use ($cartItems, $total, $uangBayar, $userId) {
            // Generate TRX ID with lock to prevent duplicates
            $trxId = $this->generateTrxId();

            $kembalian = $uangBayar - $total;

            // Create transaction
            $transaction = Transaction::create([
                'trx_id' => $trxId,
                'user_id' => $userId,
                'total_harga' => $total,
                'uang_bayar' => $uangBayar,
                'kembalian' => $kembalian,
                'tanggal' => now(),
            ]);

            // Create details and reduce stock
            foreach ($cartItems as $item) {
                $menu = $item['menu'];

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $menu->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $menu->harga,
                    'subtotal' => $menu->harga * $item['jumlah'],
                ]);

                // Reduce stock
                Menu::where('id', $menu->id)
                    ->lockForUpdate()
                    ->decrement('stok', $item['jumlah']);
            }

            return $transaction;
        });
    }
}

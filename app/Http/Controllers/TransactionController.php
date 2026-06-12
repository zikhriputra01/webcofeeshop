<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request, CartService $cartService, TransactionService $transactionService): JsonResponse
    {
        // Validate cart is not empty
        if ($cartService->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong. Tambahkan item terlebih dahulu.',
            ], 422);
        }

        $request->validate([
            'uang_bayar' => 'required|numeric|min:0',
        ]);

        $data = $cartService->calculateTotals();
        $uangBayar = (float) $request->uang_bayar;

        // Validate payment is sufficient
        if ($uangBayar < $data['total']) {
            return response()->json([
                'success' => false,
                'message' => 'Uang bayar kurang dari total pembayaran.',
            ], 422);
        }

        // Save transaction atomically
        $transaction = $transactionService->saveTransaction(
            cartItems: $data['items'],
            subtotal: $data['subtotal'],
            pajak: $data['pajak'],
            total: $data['total'],
            uangBayar: $uangBayar,
            userId: $request->user()->id
        );

        // Clear cart
        $cartService->clear();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan!',
            'trx_id' => $transaction->trx_id,
            'print_url' => route('history.print', $transaction->trx_id),
        ]);
    }
}

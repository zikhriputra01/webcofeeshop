<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Helpers\FormatHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate(['menu_id' => 'required|integer|exists:menus,id']);

        $this->cartService->addItem($request->menu_id);

        return $this->cartResponse();
    }

    /**
     * Update item quantity in cart.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'menu_id' => 'required|integer|exists:menus,id',
            'delta' => 'required|integer',
        ]);

        $this->cartService->updateItem($request->menu_id, $request->delta);

        return $this->cartResponse();
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate(['menu_id' => 'required|integer|exists:menus,id']);

        $this->cartService->removeItem($request->menu_id);

        return $this->cartResponse();
    }

    /**
     * Return unified cart response with all data needed by frontend.
     */
    private function cartResponse(): JsonResponse
    {
        $data = $this->cartService->calculateTotals();

        $items = array_map(function ($item) {
            return [
                'menu_id' => $item['menu']->id,
                'nama_menu' => $item['menu']->nama_menu,
                'icon' => $item['menu']->icon,
                'harga' => $item['menu']->harga,
                'harga_formatted' => FormatHelper::rupiah($item['menu']->harga),
                'jumlah' => $item['jumlah'],
                'subtotal' => $item['subtotal'],
                'subtotal_formatted' => FormatHelper::rupiah($item['subtotal']),
            ];
        }, $data['items']);

        return response()->json([
            'success' => true,
            'items' => $items,
            'subtotal' => $data['subtotal'],
            'subtotal_formatted' => FormatHelper::rupiah($data['subtotal']),
            'pajak' => $data['pajak'],
            'pajak_formatted' => FormatHelper::rupiah($data['pajak']),
            'total' => $data['total'],
            'total_formatted' => FormatHelper::rupiah($data['total']),
            'cart_ids' => array_column($items, 'menu_id'),
        ]);
    }
}

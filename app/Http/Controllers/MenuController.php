<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\CartService;

class MenuController extends Controller
{
    public function index(CartService $cartService)
    {
        $menus = Menu::orderBy('kategori')->orderBy('nama_menu')->get();
        $cartData = $cartService->calculateTotals();

        return view('menu.index', [
            'menus' => $menus,
            'cartItems' => $cartData['items'],
            'subtotal' => $cartData['subtotal'],
            'pajak' => $cartData['pajak'],
            'total' => $cartData['total'],
            'rawCart' => $cartService->getRawCart(),
        ]);
    }
}

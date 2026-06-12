<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * Get all cart items with menu details.
     */
    public function getItems(): array
    {
        $cart = Session::get(self::SESSION_KEY, []);
        $items = [];

        if (empty($cart)) {
            return $items;
        }

        $menus = Menu::whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $menuId => $jumlah) {
            if ($menus->has($menuId)) {
                $menu = $menus->get($menuId);
                $items[] = [
                    'menu' => $menu,
                    'jumlah' => $jumlah,
                    'subtotal' => $menu->harga * $jumlah,
                ];
            }
        }

        return $items;
    }

    /**
     * Add an item to the cart.
     */
    public function addItem(int $menuId): void
    {
        $cart = Session::get(self::SESSION_KEY, []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]++;
        } else {
            $cart[$menuId] = 1;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateItem(int $menuId, int $delta): void
    {
        $cart = Session::get(self::SESSION_KEY, []);

        if (isset($cart[$menuId])) {
            $cart[$menuId] += $delta;

            if ($cart[$menuId] <= 0) {
                unset($cart[$menuId]);
            }
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $menuId): void
    {
        $cart = Session::get(self::SESSION_KEY, []);
        unset($cart[$menuId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        $cart = Session::get(self::SESSION_KEY, []);
        return empty($cart);
    }

    /**
     * Calculate totals (subtotal, pajak, total).
     */
    public function calculateTotals(): array
    {
        $items = $this->getItems();
        $subtotal = array_sum(array_column($items, 'subtotal'));
        $pajak = round($subtotal * config('pos.pajak_rate'));
        $total = $subtotal + $pajak;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'total' => $total,
        ];
    }

    /**
     * Get raw cart data from session.
     */
    public function getRawCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}

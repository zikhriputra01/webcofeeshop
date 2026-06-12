<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // Coffee
            ['nama_menu' => 'Americano', 'kategori' => 'coffee', 'harga' => 25000, 'stok' => 50, 'icon' => '☕'],
            ['nama_menu' => 'Cappuccino', 'kategori' => 'coffee', 'harga' => 32000, 'stok' => 45, 'icon' => '☕'],
            ['nama_menu' => 'Latte', 'kategori' => 'coffee', 'harga' => 35000, 'stok' => 40, 'icon' => '☕'],
            ['nama_menu' => 'Espresso', 'kategori' => 'coffee', 'harga' => 22000, 'stok' => 60, 'icon' => '☕'],
            ['nama_menu' => 'Mocha', 'kategori' => 'coffee', 'harga' => 36000, 'stok' => 35, 'icon' => '☕'],

            // Non-Coffee
            ['nama_menu' => 'Matcha Latte', 'kategori' => 'noncoffee', 'harga' => 30000, 'stok' => 30, 'icon' => '🍵'],
            ['nama_menu' => 'Cokelat Panas', 'kategori' => 'noncoffee', 'harga' => 28000, 'stok' => 35, 'icon' => '🍵'],
            ['nama_menu' => 'Teh Tarik', 'kategori' => 'noncoffee', 'harga' => 22000, 'stok' => 40, 'icon' => '🍵'],

            // Refreshment
            ['nama_menu' => 'Lemon Tea', 'kategori' => 'refreshment', 'harga' => 20000, 'stok' => 50, 'icon' => '🥤'],
            ['nama_menu' => 'Es Jeruk', 'kategori' => 'refreshment', 'harga' => 18000, 'stok' => 45, 'icon' => '🥤'],
            ['nama_menu' => 'Smoothie Mangga', 'kategori' => 'refreshment', 'harga' => 28000, 'stok' => 25, 'icon' => '🥤'],

            // Snack
            ['nama_menu' => 'Croissant', 'kategori' => 'snack', 'harga' => 25000, 'stok' => 20, 'icon' => '🥐'],
            ['nama_menu' => 'Roti Bakar', 'kategori' => 'snack', 'harga' => 20000, 'stok' => 30, 'icon' => '🍞'],
            ['nama_menu' => 'French Fries', 'kategori' => 'snack', 'harga' => 22000, 'stok' => 25, 'icon' => '🍟'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}

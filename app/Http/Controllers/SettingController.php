<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountSettingRequest;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun.
     */
    public function akun()
    {
        return view('setting.akun', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Simpan perubahan pengaturan akun.
     */
    public function updateAkun(AccountSettingRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->nama = $request->nama;
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return back()->with('success', 'Pengaturan akun berhasil diperbarui!');
    }

    /**
     * Tampilkan halaman pengaturan toko (admin only).
     */
    public function toko()
    {
        return view('setting.toko', [
            'storeInfo' => Setting::getStoreInfo(),
        ]);
    }

    /**
     * Simpan perubahan pengaturan toko (admin only).
     */
    public function updateToko(StoreSettingRequest $request)
    {
        Setting::setValue('nama_toko', $request->nama_toko);
        Setting::setValue('alamat', $request->alamat);
        Setting::setValue('telepon', $request->telepon);

        return back()->with('success', 'Informasi toko berhasil diperbarui!');
    }

    /**
     * Tampilkan halaman kelola menu (admin only).
     */
    public function menu()
    {
        $menus = Menu::orderBy('kategori')->orderBy('nama_menu')->get();
        return view('setting.menu', [
            'menus' => $menus,
        ]);
    }

    /**
     * Simpan menu baru (admin only).
     */
    public function storeMenu(MenuRequest $request)
    {
        Menu::create($request->validated());

        return back()->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Update data menu (admin only).
     */
    public function updateMenu(MenuRequest $request, int $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->validated());

        return back()->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Hapus menu (admin only).
     */
    public function destroyMenu(int $id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->transactionDetails()->exists()) {
            return back()->withErrors(['menu' => 'Menu tidak dapat dihapus karena sudah digunakan dalam transaksi.']);
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus!');
    }
}

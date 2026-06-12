<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setValue('nama_toko', 'Brew & Co.');
        Setting::setValue('alamat', 'Jl. Kopi Nusantara No. 12, Jakarta');
        Setting::setValue('telepon', '021-1234567');
    }
}

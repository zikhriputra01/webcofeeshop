<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key (upsert).
     */
    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all store info settings as an associative array.
     */
    public static function getStoreInfo(): array
    {
        return [
            'nama_toko' => static::getValue('nama_toko', 'Brew & Co.'),
            'alamat' => static::getValue('alamat', 'Jl. Kopi Nusantara No. 12'),
            'telepon' => static::getValue('telepon', '021-1234567'),
        ];
    }
}

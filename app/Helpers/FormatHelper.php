<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format angka ke format Rupiah Indonesia.
     */
    public static function rupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format tanggal ke format Indonesia.
     */
    public static function tanggalIndonesia(\DateTimeInterface|string $date): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $hari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $dayName = $hari[$date->format('l')] ?? $date->format('l');
        $day = $date->format('d');
        $month = $bulan[(int) $date->format('m')] ?? $date->format('F');
        $year = $date->format('Y');

        return "{$dayName}, {$day} {$month} {$year}";
    }

    /**
     * Format tanggal dan waktu ke format Indonesia.
     */
    public static function tanggalWaktuIndonesia(\DateTimeInterface|string $date): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        return static::tanggalIndonesia($date) . ' ' . $date->format('H:i');
    }
}

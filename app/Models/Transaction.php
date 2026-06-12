<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'trx_id',
        'user_id',
        'total_harga',
        'uang_bayar',
        'kembalian',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'uang_bayar' => 'decimal:2',
            'kembalian' => 'decimal:2',
            'tanggal' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}

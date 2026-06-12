<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pajak Rate (Tax Rate)
    |--------------------------------------------------------------------------
    | Persentase pajak yang dikenakan pada setiap transaksi.
    */
    'pajak_rate' => 0.1,

    /*
    |--------------------------------------------------------------------------
    | Transaction ID Format
    |--------------------------------------------------------------------------
    | Format: TRX-YYYYXXXXX (4 digit tahun + 5 digit sequence)
    */
    'trx_prefix' => 'TRX-',
    'trx_sequence_length' => 5,
];

<?php

return [
    'store_name' => env('STORE_NAME', 'Toko Maju Jaya'),
    'store_address' => env('STORE_ADDRESS', 'Jl. Pasar Baru No. 12, Bandung'),
    'store_phone' => env('STORE_PHONE', '0812-0000-0000'),

    'payment_methods' => [
        'cash' => 'Tunai',
        'qris' => 'QRIS',
        'transfer' => 'Transfer',
    ],

    // Kategori beban operasional (OPEX) yang bisa dianggarkan per bulan.
    'expense_categories' => [
        'Sewa Tempat',
        'Gaji Karyawan',
        'Listrik & Air',
        'Internet & Pulsa',
        'Transportasi',
        'Kemasan & Perlengkapan',
        'Pemasaran',
        'Perawatan',
        'Lain-lain',
    ],
];

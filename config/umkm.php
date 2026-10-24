<?php

return [
    'store_name' => env('STORE_NAME', 'Aplikasi Keuangan UMKM'),
    'store_address' => env('STORE_ADDRESS', 'Toko Contoh (Instalasi Demo Interaktif)'),
    'store_phone' => env('STORE_PHONE', 'Demo Terbuka UMKM'),

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

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Sistem Kasir &amp; Manajemen Keuangan UMKM Terpadu</title>
    <meta name="description" content="Aplikasi kasir (POS) dan manajemen keuangan terpadu untuk UMKM. Otomasi HPP moving average, buku kas real-time, kontrol beban operasional dengan bukti nota, dan laporan laba bersih riil.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] font-sans text-slate-800 antialiased selection:bg-amber-100 selection:text-amber-900">

{{-- Sticky Header Navbar --}}
<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
    <div class="mx-auto flex h-16 sm:h-18 max-w-7xl items-center justify-between px-3.5 sm:px-6 lg:px-8">
        {{-- Brand Logo --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5 sm:gap-3 group min-w-0">
            @include('partials.logo', ['class' => 'h-10 w-10 sm:h-11 sm:w-11 shrink-0'])
            <div class="min-w-0">
                <p class="text-sm sm:text-base font-extrabold tracking-tight text-slate-950 leading-tight truncate">{{ config('umkm.store_name') }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 tracking-wide truncate">Sistem Kasir &amp; Akuntansi</p>
                </div>
            </div>
        </a>

        {{-- Desktop Navigation Links --}}
        <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
            <a href="#fitur" class="hover:text-slate-950 transition">Fitur Utama</a>
            <a href="#masalah-solusi" class="hover:text-slate-950 transition">Alur Bisnis</a>
            <a href="#perbandingan" class="hover:text-slate-950 transition">Keunggulan</a>
            <a href="#demo" class="hover:text-slate-950 transition">Coba Demo</a>
            <a href="{{ route('download.guide') }}" class="inline-flex items-center gap-1 text-slate-700 hover:text-amber-800 transition font-bold">
                <span>Panduan Uji Coba</span>
                <span class="text-[9.5px] font-mono bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-black">DOCX</span>
            </a>
            <a href="#faq" class="hover:text-slate-950 transition">FAQ</a>
        </nav>

        {{-- Right Actions --}}
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            @auth
                <a href="{{ auth()->user()->isOwner() ? route('dashboard') : route('pos') }}" class="btn-primary text-xs py-2 px-3 sm:px-4 font-bold shadow-xs">
                    Buka Aplikasi &rarr;
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-ghost text-xs py-1.5 sm:py-2 px-2.5 sm:px-3.5 font-bold">
                    Masuk
                </a>
                <a href="#demo" class="btn-primary text-xs py-1.5 sm:py-2 px-3 sm:px-4 font-bold shadow-xs">
                    Coba Demo
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- Hero Section --}}
<section class="relative overflow-hidden bg-gradient-to-b from-white via-slate-50/50 to-[#F8FAFC] pt-10 pb-16 sm:pt-16 sm:pb-24 lg:pt-20 lg:pb-28">
    {{-- Ambient radial background glow --}}
    <div class="pointer-events-none absolute -top-40 left-1/2 h-[450px] w-[700px] -translate-x-1/2 rounded-full bg-gradient-to-tr from-amber-400/10 to-slate-900/5 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        {{-- Eyebrow badge --}}
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/60 bg-amber-50/80 px-3 sm:px-4 py-1 text-[10.5px] sm:text-xs font-bold text-amber-900 shadow-xs backdrop-blur-xs mb-6 sm:mb-8 max-w-full text-center leading-normal">
            <span class="flex h-2 w-2 shrink-0 rounded-full bg-amber-500 animate-pulse"></span>
            <span>SISTEM KASIR &amp; KEUANGAN UMKM TERPADU · OPEN SOURCE</span>
        </div>

        {{-- Main Headline --}}
        <h1 class="mx-auto max-w-4xl text-2xl font-black tracking-tight text-slate-950 sm:text-5xl lg:text-6xl leading-[1.2] sm:leading-[1.12]">
            Bukan Sekadar Kasir POS Biasa.<br>
            <span class="bg-gradient-to-r from-slate-950 via-slate-800 to-amber-700 bg-clip-text text-transparent">
                Ketahui Laba Bersih Riil
            </span> Toko Anda Setiap Hari.
        </h1>

        {{-- Subtitle --}}
        <p class="mx-auto mt-4 sm:mt-6 max-w-2xl text-xs sm:text-base font-medium leading-relaxed text-slate-600">
            Aplikasi kasir terpadu yang tidak hanya mencatat transaksi penjualan, tapi otomatis menghitung <strong class="text-slate-900 font-bold">HPP moving average</strong>, mengawasi <strong class="text-slate-900 font-bold">beban operasional dengan upload nota</strong>, dan mengunci selisih laci kasir.
        </p>

        {{-- Action Buttons --}}
        <div class="mt-7 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-2.5 sm:gap-3.5 max-w-md sm:max-w-none mx-auto">
            <a href="#demo" class="btn-primary text-xs sm:text-sm py-3 sm:py-3.5 px-6 font-extrabold shadow-md justify-center">
                🚀 Coba Demo 1-Klik Sekarang &rarr;
            </a>
            <a href="mailto:jaddadeveloper@gmail.com?subject=Konsultasi%20Aplikasi%20Keuangan%20UMKM" class="btn-ghost text-xs sm:text-sm py-3 sm:py-3.5 px-5 font-bold shadow-xs inline-flex items-center justify-center gap-2 border-slate-300 text-slate-800 hover:bg-slate-50">
                <span>✉️</span> Hubungi via Email
            </a>
            <a href="{{ route('login') }}" class="btn-ghost text-xs sm:text-sm py-3 sm:py-3.5 px-5 font-bold shadow-xs justify-center">
                Masuk ke Akun
            </a>
        </div>
        <div class="mt-4 flex flex-wrap items-center justify-center gap-2 sm:gap-4 text-[11px] sm:text-xs font-semibold text-slate-500">
            <span>✓ Tanpa Registrasi untuk Coba Demo</span>
            <span class="hidden sm:inline">·</span>
            <a href="{{ route('download.guide') }}" class="text-slate-800 hover:text-amber-800 underline underline-offset-4 font-bold inline-flex items-center gap-1 transition">
                <span>📥 Unduh Panduan Uji Coba (.docx) ↓</span>
            </a>
            <span class="hidden sm:inline">·</span>
            <a href="https://github.com/bipisurel-bps/aplikasikeuanganumkmgratis" target="_blank" rel="noopener noreferrer" class="text-slate-700 hover:text-slate-950 underline underline-offset-4 font-bold inline-flex items-center gap-1">
                Kode Sumber di GitHub ↗
            </a>
        </div>

        {{-- Key Trust Metrics Row --}}
        <div class="mx-auto mt-10 sm:mt-12 grid max-w-4xl grid-cols-2 gap-2.5 sm:gap-4 pt-6 sm:pt-8 border-t border-slate-200/80 sm:grid-cols-4 text-center sm:text-left">
            <div class="rounded-xl border border-slate-200/70 bg-white/70 p-3 sm:p-4 shadow-xs">
                <p class="font-mono text-lg sm:text-2xl font-black text-slate-950">Rp 0,-</p>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5">Bebas Langganan</p>
            </div>
            <div class="rounded-xl border border-slate-200/70 bg-white/70 p-3 sm:p-4 shadow-xs">
                <p class="font-mono text-lg sm:text-2xl font-black text-slate-950">Auto HPP</p>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5">Moving Average</p>
            </div>
            <div class="rounded-xl border border-slate-200/70 bg-white/70 p-3 sm:p-4 shadow-xs">
                <p class="font-mono text-lg sm:text-2xl font-black text-slate-950">Real-Time</p>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5">Jurnal Kas Otomatis</p>
            </div>
            <div class="rounded-xl border border-slate-200/70 bg-white/70 p-3 sm:p-4 shadow-xs">
                <p class="font-mono text-lg sm:text-2xl font-black text-slate-950">100%</p>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5">Data Milik Anda</p>
            </div>
        </div>
    </div>
</section>
</section>

{{-- Interactive Software Showcase Window (Mockup) --}}
<section class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 -mt-6 mb-24" x-data="{ tab: 'dashboard' }">
    <div class="card overflow-hidden rounded-2xl border-slate-300 shadow-2xl bg-slate-950 text-white">
        {{-- Window Control Bar --}}
        <div class="flex items-center justify-between border-b border-slate-800 bg-slate-900/90 px-3 sm:px-4 py-2.5 sm:py-3">
            <div class="flex items-center gap-1.5 sm:gap-2">
                <span class="h-2.5 w-2.5 sm:h-3 sm:w-3 rounded-full bg-rose-500"></span>
                <span class="h-2.5 w-2.5 sm:h-3 sm:w-3 rounded-full bg-amber-500"></span>
                <span class="h-2.5 w-2.5 sm:h-3 sm:w-3 rounded-full bg-emerald-500"></span>
                <span class="ml-1.5 font-mono text-[10px] sm:text-[11px] text-slate-400 font-bold hidden sm:inline">demo.aplikasikeuanganumkm.web.id · Pratinjau Ruang Kerja</span>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800">
                <button @click="tab = 'dashboard'" :class="tab === 'dashboard' ? 'bg-amber-400 text-slate-950 font-black' : 'text-slate-400 hover:text-white'" class="rounded-lg px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold transition">
                    Dashboard Laba
                </button>
                <button @click="tab = 'pos'" :class="tab === 'pos' ? 'bg-amber-400 text-slate-950 font-black' : 'text-slate-400 hover:text-white'" class="rounded-lg px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold transition">
                    Layar Kasir
                </button>
            </div>
        </div>

        {{-- Tab Content 1: Dashboard --}}
        <div x-show="tab === 'dashboard'" class="p-4 sm:p-6 lg:p-8 bg-slate-900 space-y-4 sm:space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3.5 sm:p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Omzet</p>
                    <p class="font-mono text-lg sm:text-2xl font-black text-white mt-1">Rp 42.738.600</p>
                    <p class="text-[10.5px] sm:text-[11px] text-slate-500 mt-1">418 Transaksi Berhasil</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3.5 sm:p-4">
                    <p class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">HPP (Modal Barang)</p>
                    <p class="font-mono text-lg sm:text-2xl font-black text-amber-400 mt-1">Rp 33.314.801</p>
                    <p class="text-[10.5px] sm:text-[11px] text-slate-500 mt-1">Laba Kotor: Rp 9.423.799</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3.5 sm:p-4">
                    <p class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Beban Operasional</p>
                    <p class="font-mono text-lg sm:text-2xl font-black text-rose-400 mt-1">Rp 6.080.000</p>
                    <p class="text-[10.5px] sm:text-[11px] text-slate-500 mt-1">Sewa, Listrik, Gaji Kasir</p>
                </div>
                <div class="rounded-xl border border-amber-400/40 bg-gradient-to-br from-amber-950/40 via-slate-950 to-slate-950 p-3.5 sm:p-4 ring-1 ring-amber-400/30">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black text-amber-300 uppercase tracking-wider">Laba Bersih Riil</p>
                        <span class="rounded bg-amber-400/20 px-1.5 py-0.5 font-mono text-[9.5px] sm:text-[10px] font-bold text-amber-300">7.8% Net</span>
                    </div>
                    <p class="font-mono text-lg sm:text-2xl font-black text-amber-300 mt-1">Rp 3.343.799</p>
                    <p class="text-[10.5px] sm:text-[11px] text-slate-400 mt-1">Uang untung riil Anda</p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <span class="text-[11px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider">Grafik Pergerakan Omzet Harian</span>
                    <span class="font-mono text-[11px] sm:text-xs text-amber-400">Puncak: Rp 3.850.000 / hari</span>
                </div>
                <div class="flex h-28 sm:h-32 items-end gap-1.5 overflow-x-auto border-b border-slate-800 pb-2">
                    <div class="flex-1 bg-slate-800 rounded-t h-[40%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[65%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[50%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[80%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[95%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[70%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[85%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[60%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-slate-800 rounded-t h-[90%] hover:bg-amber-400 transition"></div>
                    <div class="flex-1 bg-amber-500 rounded-t h-[100%] shadow-md"></div>
                </div>
            </div>
        </div>

        {{-- Tab Content 2: POS Screen --}}
        <div x-show="tab === 'pos'" class="p-4 sm:p-6 lg:p-8 bg-slate-900" style="display: none;">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-3.5 sm:space-y-4">
                    <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950 p-2.5 sm:p-3">
                        <span class="text-[11px] sm:text-xs font-mono text-slate-400 truncate">Scan SKU / Barcode: <b>MNM-001</b></span>
                        <span class="rounded bg-slate-800 px-2 py-0.5 font-mono text-[10px] text-amber-400 font-bold shrink-0">READY</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-800 bg-slate-950 p-2.5 sm:p-3">
                            <span class="text-[10px] font-mono text-slate-500">MNM-001</span>
                            <p class="font-bold text-xs text-white mt-1 truncate">Air Mineral 600ml</p>
                            <p class="font-mono text-xs font-black text-amber-400 mt-1.5">Rp 3.500</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950 p-2.5 sm:p-3">
                            <span class="text-[10px] font-mono text-slate-500">SMB-001</span>
                            <p class="font-bold text-xs text-white mt-1 truncate">Beras Premium 5kg</p>
                            <p class="font-mono text-xs font-black text-amber-400 mt-1.5">Rp 76.000</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950 p-2.5 sm:p-3 col-span-2 sm:col-span-1">
                            <span class="text-[10px] font-mono text-slate-500">SMB-002</span>
                            <p class="font-bold text-xs text-white mt-1 truncate">Minyak Goreng 1L</p>
                            <p class="font-mono text-xs font-black text-amber-400 mt-1.5">Rp 18.500</p>
                        </div>
                    </div>
                </div>

                {{-- Cart Mockup --}}
                <div class="rounded-xl border border-slate-800 bg-slate-950 p-3.5 sm:p-4 space-y-2.5 sm:space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white">Keranjang Kasir</span>
                        <span class="rounded bg-slate-800 px-1.5 py-0.5 font-mono text-[10px] text-slate-300">2 Item</span>
                    </div>
                    <div class="space-y-1.5 text-xs font-mono">
                        <div class="flex justify-between text-slate-300"><span>1× Beras 5kg</span><span>Rp 76.000</span></div>
                        <div class="flex justify-between text-slate-300"><span>2× Air Mineral</span><span>Rp 7.000</span></div>
                    </div>
                    <div class="border-t border-dashed border-slate-800 pt-2 flex justify-between text-sm font-black text-white">
                        <span>TOTAL</span><span class="text-amber-400 font-mono">Rp 83.000</span>
                    </div>
                    <div class="rounded-lg bg-emerald-950/60 border border-emerald-800/80 p-2 text-center text-[11px] sm:text-xs font-bold text-emerald-400">
                        Bayar Tunai Rp 100.000 · Kembali Rp 17.000
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Masalah Nyata vs Solusi Terpadu --}}
<section id="masalah-solusi" class="py-20 border-t border-slate-200/80 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600">Alur Bisnis &amp; Keuangan Nyata</span>
            <h2 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl mt-2">
                Kenapa Banyak UMKM Merasa Omzetnya Besar, Tapi Saldonya Selalu Pas-pasan?
            </h2>
            <p class="mt-4 text-sm text-slate-600 leading-relaxed">
                Kebanyakan aplikasi kasir di pasaran hanya berhenti di pencatatan transaksi penjualan, tanpa menghubungkannya ke harga pokok pembelian yang fluktuatif dan pengeluaran operasional sehari-hari.
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            {{-- Point 1 --}}
            <div class="card p-6 border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 font-bold mb-4">
                    ✕
                </div>
                <h3 class="text-sm font-extrabold text-slate-950">Terjebak Angka Omzet</h3>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    Omzet 50 juta sebulan terlihat besar, tapi jika modal belanja barang 42 juta dan beban operasional 7 juta, laba bersih Anda sebenarnya hanya 1 juta.
                </p>
                <div class="mt-4 border-t border-slate-200 pt-3">
                    <p class="text-[11px] font-bold text-emerald-700">✓ Solusi: Formula Laba Bersih Riil (Omzet − HPP − Beban)</p>
                </div>
            </div>

            {{-- Point 2 --}}
            <div class="card p-6 border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 font-bold mb-4">
                    ✕
                </div>
                <h3 class="text-sm font-extrabold text-slate-950">Harga Kulakan Naik Turun</h3>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    Minyak goreng dibeli minggu lalu Rp 14.000, minggu ini kulakan Rp 16.000. Jika HPP tidak dihitung rata-rata, margin keuntungan Anda keliru.
                </p>
                <div class="mt-4 border-t border-slate-200 pt-3">
                    <p class="text-[11px] font-bold text-emerald-700">✓ Solusi: Auto-Kalkulasi Moving Average HPP saat restock</p>
                </div>
            </div>

            {{-- Point 3 --}}
            <div class="card p-6 border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 font-bold mb-4">
                    ✕
                </div>
                <h3 class="text-sm font-extrabold text-slate-950">Laci Kasir Sering Selisih</h3>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    Saat pergantian shift kasir, uang fisik di laci tidak cocok dengan komputer. Pemilik tidak tahu uangnya kurang di mana.
                </p>
                <div class="mt-4 border-t border-slate-200 pt-3">
                    <p class="text-[11px] font-bold text-emerald-700">✓ Solusi: Shift Kasir Wajib Hitung Modal Awal &amp; Uang Fisik</p>
                </div>
            </div>

            {{-- Point 4 --}}
            <div class="card p-6 border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 font-bold mb-4">
                    ✕
                </div>
                <h3 class="text-sm font-extrabold text-slate-950">Biaya Operasional Tercecer</h3>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    Uang toko sering diambil untuk bayar token listrik, kantong kresek, atau bensin tanpa nota dan tanpa batasan anggaran bulanan.
                </p>
                <div class="mt-4 border-t border-slate-200 pt-3">
                    <p class="text-[11px] font-bold text-emerald-700">✓ Solusi: Catat Beban + Foto Nota &amp; Monitor Plafon Anggaran</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 4 Pilar Fitur Utama --}}
<section id="fitur" class="py-20 bg-[#F8FAFC]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600">Fitur Terpadu Tanpa Celah</span>
            <h2 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl mt-2">
                Dirancang Khusus untuk Operasional Sehari-hari Toko &amp; Warung
            </h2>
            <p class="mt-4 text-sm text-slate-600">
                Semua bagian terhubung secara atomik: transaksi kasir memotong stok dan membukukan mutasi uang ke buku kas dalam satu ketukan.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Fitur 1 --}}
            <div class="card p-7 border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-amber-400 font-bold">
                            POS
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950">Layar Kasir (POS) Kilat &amp; Siap Layar Sentuh</h3>
                            <p class="text-xs text-slate-500">Cepat melayani antrean pembeli di HP, tablet, maupun komputer meja</p>
                        </div>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 mt-5">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Cari &amp; Scan Barcode SKU Instan:</b> Masukkan SKU atau nama lalu tekan Enter untuk langsung memasukkan ke keranjang.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Pecahan Uang Pas Cepat:</b> Tombol sekali sentuh untuk uang pas, kelipatan Rp 5.000, 10.000, 50.000, dan 100.000.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Struk Belanja Thermal Siap Cetak:</b> Format nota belanja rapi yang siap langsung dicetak ke printer kasir bluetooth atau kabel.</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Fitur 2 --}}
            <div class="card p-7 border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-bold">
                            HPP
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950">Formula HPP Moving Average &amp; Restock Supplier</h3>
                            <p class="text-xs text-slate-500">Harga pokok bergerak dinamis mengikuti setiap faktur pembelian baru</p>
                        </div>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 mt-5">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Formula Akuntansi Resmi:</b> <code class="font-mono text-[11px] bg-slate-100 px-1 py-0.5 rounded">(Stok Lama × HPP Lama + Qty Baru × Harga Baru) / Total Stok</code>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Peringatan Stok Menipis:</b> Tandai otomatis produk yang berada di bawah batas minimum toko.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Pencatatan Pemasok:</b> Pantau riwayat belanja grosir ke masing-masing supplier toko.</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Fitur 3 --}}
            <div class="card p-7 border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-800 text-white font-bold">
                            KAS
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950">Buku Kas Terpadu Tanpa Jurnal Manual</h3>
                            <p class="text-xs text-slate-500">Setiap uang masuk dan keluar langsung memiliki jejak audit otomatis</p>
                        </div>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 mt-5">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Pencatatan Otomatis:</b> Penjualan kasir masuk sebagai Debit, restock barang &amp; beban operasional masuk sebagai Kredit.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Integritas Data:</b> Jika beban dihapus, mutasi kas terkait dibatalkan seketika agar saldo kas tidak menyimpang.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Dukungan Multi-Metode:</b> Memisahkan mutasi kas tunai, QRIS, dan transfer bank.</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Fitur 4 --}}
            <div class="card p-7 border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-700 text-white font-bold">
                            OPEX
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950">Kontrol Beban Operasional &amp; Plafon Anggaran</h3>
                            <p class="text-xs text-slate-500">Mencegah pengeluaran bocor halus dengan bukti nota dan pagu bulanan</p>
                        </div>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-600 mt-5">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Unggah Bukti Nota / Kuitansi:</b> Simpan foto nota (JPG/PNG/PDF) secara privat dan aman di server Anda.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Bar Indikator Sisa Anggaran:</b> Warna visual langsung berubah jika pengeluaran mendekati 80% atau melampaui pagu.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><b>Kategori Beban Fleksibel:</b> Pos sewa kios, gaji kasir, token listrik, pulsa, transportasi, dan kemasan.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Interactive 1-Click Sandbox Section (Pintu Masuk Demo) --}}
<section id="demo" class="py-20 bg-slate-950 text-white relative overflow-hidden border-t border-slate-800">
    {{-- Ambient light --}}
    <div class="pointer-events-none absolute -bottom-40 right-1/2 h-[450px] w-[600px] rounded-full bg-amber-500/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-black uppercase tracking-widest text-amber-400">Sandbox Uji Coba Langsung</span>
        <h2 class="text-3xl font-black tracking-tight text-white sm:text-5xl mt-2">
            Coba Langsung Aplikasinya Sekarang.<br>
            <span class="text-slate-400">Pilih Peran dan Masuk dalam 1 Klik!</span>
        </h2>
        <p class="mx-auto mt-4 max-w-xl text-xs sm:text-sm text-slate-400 leading-relaxed">
            Data demo 14 hari dengan 400+ transaksi terpasang dalam mode pengamat (Read-Only). Klik salah satu tombol di bawah untuk langsung mencoba tanpa perlu mengisi formulir pendaftaran.
        </p>

        {{-- Callout Panduan Uji Coba (Word .docx) --}}
        <div class="mt-7 inline-flex flex-col sm:flex-row items-center justify-between gap-3.5 rounded-2xl border border-slate-800 bg-slate-900/90 px-4 sm:px-6 py-3.5 max-w-2xl mx-auto text-left shadow-lg">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-400/10 text-amber-400 border border-amber-400/20 text-lg">
                    📋
                </span>
                <div>
                    <p class="text-xs sm:text-sm font-extrabold text-white">Ingin menguji fitur secara terarah &amp; bertahap?</p>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Tersedia dokumen resmi berisi 8 skenario pengujian &amp; lembar evaluasi toko.</p>
                </div>
            </div>
            <a href="{{ route('download.guide') }}" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2 text-xs font-extrabold text-slate-950 hover:bg-amber-300 transition shadow-xs w-full sm:w-auto justify-center">
                <span>Unduh Word (.docx)</span>
                <span>↓</span>
            </a>
        </div>

        {{-- Role Cards --}}
        <div class="mt-8 sm:mt-10 grid gap-6 md:grid-cols-2 max-w-4xl mx-auto text-left">
            {{-- Card Owner --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-7 hover:border-amber-400/50 transition-all duration-200 flex flex-col justify-between shadow-xl">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="badge bg-amber-400/20 text-amber-300 border border-amber-400/40 font-mono text-[10px]">
                            PERAN: OWNER / PEMILIK
                        </span>
                        <span class="text-xs text-slate-400 font-mono">owner@umkm.test</span>
                    </div>

                    <h3 class="text-lg font-extrabold text-white">Masuk Sebagai Pemilik Toko</h3>
                    <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                        Akses penuh ke seluruh data bisnis: Dashboard Laba Bersih riil, grafik penjualan harian, pengaturan anggaran operasional bulanan, buku kas, katalog produk &amp; HPP.
                    </p>

                    <div class="mt-5 space-y-2 text-xs text-slate-300">
                        <p class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Melihat laporan laba bersih riil</p>
                        <p class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Mengatur plafon anggaran operasional</p>
                        <p class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Mencatat faktur restock supplier</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('login.demo') }}" class="mt-7">
                    @csrf
                    <input type="hidden" name="role" value="owner">
                    <button class="btn-primary w-full py-3 text-xs font-bold bg-amber-400 text-slate-950 hover:bg-amber-300 shadow-md">
                        Buka Dashboard Pemilik (Owner) &rarr;
                    </button>
                </form>
            </div>

            {{-- Card Kasir --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-7 hover:border-slate-700 transition-all duration-200 flex flex-col justify-between shadow-xl">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="badge bg-slate-800 text-slate-300 border border-slate-700 font-mono text-[10px]">
                            PERAN: KASIR TOKO
                        </span>
                        <span class="text-xs text-slate-400 font-mono">kasir@umkm.test</span>
                    </div>

                    <h3 class="text-lg font-extrabold text-white">Masuk Sebagai Kasir Toko</h3>
                    <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                        Fokus murni pada operasional meja kasir: buka shift dengan hitung modal awal di laci, proses keranjang belanja pembeli dengan barcode scan, dan cetak struk belanja.
                    </p>

                    <div class="mt-5 space-y-2 text-xs text-slate-300">
                        <p class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Layar POS kasir cepat &amp; ramah sentuh</p>
                        <p class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Cetak struk belanja &amp; tombol uang pas</p>
                        <p class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Rekonsiliasi tutup shift laci kasir</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('login.demo') }}" class="mt-7">
                    @csrf
                    <input type="hidden" name="role" value="kasir">
                    <button class="btn-ghost w-full py-3 text-xs font-bold border-slate-700 bg-slate-800 text-white hover:bg-slate-700">
                        Buka Layar Kasir (POS) &rarr;
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-10 sm:mt-12 rounded-2xl border border-slate-800 bg-slate-900/95 p-6 sm:p-8 max-w-2xl mx-auto text-center shadow-xl">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-400/10 text-amber-400 border border-amber-400/20 mb-3.5">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h4 class="text-base sm:text-lg font-extrabold text-white">Ingin Memasang Aplikasi Ini untuk Toko Anda Sendiri?</h4>
            <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed max-w-lg mx-auto">
                Tersedia panduan instalasi mandiri di laptop atau server toko, konfigurasi printer nota thermal, serta konsultasi teknis. Silakan hubungi pengembang melalui email:
            </p>
            <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="mailto:jaddadeveloper@gmail.com?subject=Konsultasi%20Instalasi%20Aplikasi%20Keuangan%20UMKM" class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-400 px-6 py-3 text-xs sm:text-sm font-extrabold text-slate-950 hover:bg-amber-300 transition shadow-md w-full sm:w-auto">
                    <span>✉️</span> Hubungi: jaddadeveloper@gmail.com
                </a>
            </div>
        </div>

        <div class="mt-8 sm:mt-10 text-xs text-slate-500">
            Sudah memiliki akun terdaftar sendiri? <a href="{{ route('login') }}" class="text-amber-400 font-bold hover:underline">Masuk lewat halaman login &rarr;</a>
        </div>
    </div>
</section>

{{-- Tabel Perbandingan --}}
<section id="perbandingan" class="py-12 sm:py-20 bg-white border-t border-slate-200/80">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-14">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600">Mengapa Aplikasi Ini Berbeda?</span>
            <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-950 mt-2">
                Perbandingan dengan Solusi Lain di Pasaran
            </h2>
        </div>

        <div class="card overflow-hidden border-slate-200">
            <div class="px-4 py-2 text-[11px] font-semibold text-slate-500 bg-slate-50 border-b border-slate-100 sm:hidden flex items-center justify-between">
                <span>Tabel Perbandingan Fitur</span>
                <span class="text-amber-700 font-bold">Geser tabel &rarr;</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="th py-3.5">Kemampuan &amp; Fitur</th>
                            <th class="th py-3.5 text-center">Buku Manual / Catatan</th>
                            <th class="th py-3.5 text-center">Software POS Berlangganan</th>
                            <th class="th py-3.5 text-center bg-amber-50/80 text-amber-950 font-black">Aplikasi UMKM Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="td font-bold">Hitung Laba Bersih Riil (Omzet - HPP - Beban)</td>
                            <td class="td text-center text-slate-400">Manual / Rentan Lupa</td>
                            <td class="td text-center text-slate-400">Jarang (Hanya Omzet)</td>
                            <td class="td text-center bg-amber-50/40 font-bold text-emerald-800">✓ Otomatis Real-Time</td>
                        </tr>
                        <tr>
                            <td class="td font-bold">Formula HPP Moving Average Saat Restock</td>
                            <td class="td text-center text-slate-400">Sangat Sulit Dihitung</td>
                            <td class="td text-center text-slate-400">Perlu Modul Enterprise Mahal</td>
                            <td class="td text-center bg-amber-50/40 font-bold text-emerald-800">✓ Bawaan Standar</td>
                        </tr>
                        <tr>
                            <td class="td font-bold">Buku Kas Jurnal Mutasi Otomatis (Zero Input)</td>
                            <td class="td text-center text-slate-400">Manual 2 Kali Tulis</td>
                            <td class="td text-center text-slate-400">Terpisah dari Kasir</td>
                            <td class="td text-center bg-amber-50/40 font-bold text-emerald-800">✓ Atomik &amp; Otomatis</td>
                        </tr>
                        <tr>
                            <td class="td font-bold">Unggah Bukti Nota Beban &amp; Bar Plafon Anggaran</td>
                            <td class="td text-center text-slate-400">Nota Kertas Cepat Hilang</td>
                            <td class="td text-center text-slate-400">Tidak Tersedia</td>
                            <td class="td text-center bg-amber-50/40 font-bold text-emerald-800">✓ Arsip Digital &amp; Plafon</td>
                        </tr>
                        <tr>
                            <td class="td font-bold">Biaya Langganan Bulanan</td>
                            <td class="td text-center text-slate-700 font-semibold">Gratis</td>
                            <td class="td text-center text-rose-600 font-bold">Rp 150rb - 500rb / bln</td>
                            <td class="td text-center bg-amber-50/40 font-bold text-emerald-800">✓ Rp 0,- (Open Source)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
<section id="faq" class="py-12 sm:py-20 bg-[#F8FAFC] border-t border-slate-200/80">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-14">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600">Pertanyaan yang Sering Diajukan</span>
            <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-950 mt-2">
                Frequently Asked Questions (FAQ)
            </h2>
        </div>

        <div class="space-y-3.5 sm:space-y-4" x-data="{ active: null }">
            <div class="card p-4 sm:p-5 border-slate-200 cursor-pointer" @click="active = (active === 1 ? null : 1)">
                <div class="flex items-center justify-between font-bold text-xs sm:text-sm text-slate-950">
                    <span>Apakah aplikasi ini bisa dijalankan di HP atau tablet biasa?</span>
                    <span class="text-slate-400 text-base font-mono" x-text="active === 1 ? '−' : '+'"></span>
                </div>
                <div x-show="active === 1" class="mt-2.5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5" style="display: none;">
                    Bisa sekali! Seluruh antarmuka kasir dan laporan keuangan telah dioptimasi dengan desain mobile-first yang sangat nyaman digunakan pada layar sentuh ponsel Android, tablet, iPad, maupun laptop.
                </div>
            </div>

            <div class="card p-4 sm:p-5 border-slate-200 cursor-pointer" @click="active = (active === 2 ? null : 2)">
                <div class="flex items-center justify-between font-bold text-xs sm:text-sm text-slate-950">
                    <span>Bagaimana cara kerja perhitungan HPP Moving Average?</span>
                    <span class="text-slate-400 text-base font-mono" x-text="active === 2 ? '−' : '+'"></span>
                </div>
                <div x-show="active === 2" class="mt-2.5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5" style="display: none;">
                    Setiap kali Anda mencatat pembelian restock dari supplier, sistem akan mengalikan stok sisa lama dengan harga pokok lama, lalu menambahkannya dengan belanjaan baru, dan membaginya dengan total stok keseluruhan. Dengan cara ini, harga modal produk Anda selalu adil dan akurat meskipun harga di pasar sering berfluktuasi.
                </div>
            </div>

            <div class="card p-4 sm:p-5 border-slate-200 cursor-pointer" @click="active = (active === 3 ? null : 3)">
                <div class="flex items-center justify-between font-bold text-xs sm:text-sm text-slate-950">
                    <span>Apakah data transaksi toko saya tersimpan aman dan privat?</span>
                    <span class="text-slate-400 text-base font-mono" x-text="active === 3 ? '−' : '+'"></span>
                </div>
                <div x-show="active === 3" class="mt-2.5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5" style="display: none;">
                    Sangat aman. Aplikasi ini bersifat *self-hosted* dan mandiri. Seluruh database, foto nota, dan riwayat omzet toko berada 100% di server atau perangkat Anda sendiri, tanpa ada pihak ketiga yang bisa melihat atau mengambil data rahasia bisnis Anda.
                </div>
            </div>

            <div class="card p-4 sm:p-5 border-slate-200 cursor-pointer" @click="active = (active === 4 ? null : 4)">
                <div class="flex items-center justify-between font-bold text-xs sm:text-sm text-slate-950">
                    <span>Apakah ada batasan jumlah produk atau transaksi?</span>
                    <span class="text-slate-400 text-base font-mono" x-text="active === 4 ? '−' : '+'"></span>
                </div>
                <div x-show="active === 4" class="mt-2.5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-2.5" style="display: none;">
                    Tidak ada batasan sama sekali (unlimited). Anda bebas memasukkan ribuan produk, mencatat puluhan ribu transaksi, dan mengunggah nota sebanyak kapasitas memori server Anda.
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer with Developer Credit --}}
<footer class="border-t border-slate-200 bg-white py-10 sm:py-14 text-xs text-slate-600">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-5 sm:gap-6 pb-6 sm:pb-8 border-b border-slate-100 text-center md:text-left">
            <div class="flex items-center gap-3">
                @include('partials.logo', ['class' => 'h-9 w-9 sm:h-10 sm:w-10'])
                <div>
                    <p class="font-extrabold text-sm text-slate-950">{{ config('umkm.store_name') }}</p>
                    <p class="text-[11px] text-slate-500">Sistem Kasir &amp; Manajemen Keuangan UMKM Terpadu</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 font-bold text-slate-700">
                <a href="#fitur" class="hover:text-slate-950">Fitur Utama</a>
                <a href="#masalah-solusi" class="hover:text-slate-950">Alur Bisnis</a>
                <a href="#demo" class="hover:text-slate-950">Uji Coba Demo</a>
                <a href="{{ route('download.guide') }}" class="hover:text-amber-700 text-slate-900 font-extrabold">Panduan Uji Coba (Word) ↓</a>
                <a href="{{ route('login') }}" class="hover:text-slate-950">Masuk Akun</a>
                <a href="https://github.com/bipisurel-bps/aplikasikeuanganumkmgratis" target="_blank" rel="noopener noreferrer" class="hover:text-slate-950">GitHub</a>
                <a href="mailto:jaddadeveloper@gmail.com" class="hover:text-amber-700">Email Pengembang</a>
            </div>
        </div>

        <div class="pt-6 sm:pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div>
                <p class="text-slate-700">
                    Dikembangkan oleh <strong class="font-bold text-slate-950">bipi (jaddadeveloper)</strong> untuk kemanfaatan bersama seluas-luasnya.
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Open source · Lisensi MIT · Dibangun dengan Laravel 12, Tailwind CSS 4, &amp; Alpine.js.
                </p>
            </div>

            <div class="text-[11px] font-mono text-slate-400">
                &copy; {{ date('Y') }} {{ config('umkm.store_name') }} · Sistem Akuntansi UMKM Terpadu Open Source untuk Pelaku Usaha Indonesia.
            </div>
        </div>
    </div>
</footer>

</body>
</html>

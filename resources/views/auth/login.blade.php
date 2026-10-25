<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <title>Masuk · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800">
<div class="min-h-screen grid lg:grid-cols-[1.1fr_1fr] bg-slate-50">
    {{-- Left Showcase Side --}}
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-12 text-white border-r border-slate-800">
        {{-- Subtle ambient background glow --}}
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-slate-800/40 blur-3xl pointer-events-none"></div>

        {{-- Top Brand Identity --}}
        <div class="relative z-10">
            <a href="/" class="flex items-center gap-3.5 mb-8 group inline-flex">
                @include('partials.logo', ['class' => 'h-12 w-12'])
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-white">{{ config('umkm.store_name') }}</h2>
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-400/90">Sistem Kasir &amp; Manajemen Keuangan UMKM</p>
                </div>
            </a>

            <div class="max-w-md">
                <h1 class="text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Kelola Penjualan, Stok, &amp; Laba Bersih Riil dalam Satu Aplikasi.
                </h1>
                <p class="mt-4 text-sm font-medium leading-relaxed text-slate-300">
                    Dirancang dengan presisi akuntansi untuk UMKM modern: otomasi pencatatan kas dari meja kasir hingga kalkulasi HPP moving average dan anggaran bulanan.
                </p>
            </div>

            {{-- Feature Checklist --}}
            <div class="mt-8 space-y-3.5 text-xs font-semibold text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-400/15 text-amber-400 ring-1 ring-amber-400/30 font-bold">✓</span>
                    <span>Point of Sales (POS) cepat, responsif di HP/tablet, struk belanja &amp; QRIS</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-400/15 text-amber-400 ring-1 ring-amber-400/30 font-bold">✓</span>
                    <span>Kalkulasi HPP otomatis metode Moving Average &amp; restock supplier</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-400/15 text-amber-400 ring-1 ring-amber-400/30 font-bold">✓</span>
                    <span>Buku kas terpadu &amp; beban operasional dengan bukti upload nota</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-400/15 text-amber-400 ring-1 ring-amber-400/30 font-bold">✓</span>
                    <span>Laporan laba bersih riil: <code class="font-mono text-amber-300">Omzet − HPP − Beban</code></span>
                </div>
            </div>
        </div>

        {{-- Left Footer Attribution --}}
        <div class="relative z-10 border-t border-slate-800/80 pt-6 text-xs text-slate-400">
            <p>Dikembangkan oleh <strong class="font-bold text-white">bipi (jaddadeveloper)</strong> untuk kemanfaatan bersama seluas-luasnya.</p>
            <p class="mt-1 text-[11px] text-slate-500">Open source · Lisensi MIT · Terpadu &amp; Mandiri</p>
        </div>
    </div>

    {{-- Right Form Side --}}
    <div class="flex items-center justify-center p-4 sm:p-10 lg:p-16">
        <div class="w-full max-w-md">
            {{-- Mobile Brand Logo --}}
            <div class="mb-6 text-center lg:hidden">
                <a href="/" class="mx-auto mb-2 flex items-center justify-center">
                    @include('partials.logo', ['class' => 'h-12 w-12'])
                </a>
                <h1 class="text-lg font-extrabold text-slate-950">{{ config('umkm.store_name') }}</h1>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Akses Masuk &amp; Demo Interaktif</p>
            </div>

            <div class="card p-5 sm:p-8 shadow-md border-slate-200">
                <div class="mb-5 sm:mb-6">
                    <a href="/" class="text-xs font-bold text-amber-600 hover:text-amber-700 inline-block mb-2.5">&larr; Kembali ke Beranda</a>
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-slate-950">Masuk ke Akun Anda</h2>
                    <p class="mt-1 text-xs font-medium text-slate-500">Masukkan kredensial pengguna, atau langsung coba mode demo di bawah.</p>
                </div>

                {{-- 1-Click Demo Card (Aligned with SAS concept) --}}
                <div class="mb-5 sm:mb-6 rounded-2xl border border-amber-300 bg-amber-50/80 p-3.5 sm:p-4 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-slate-950 text-xs mb-1.5">
                        <span class="text-amber-600 text-sm">⚡</span>
                        <span>Ingin Coba Langsung Tanpa Kata Sandi?</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-600 mb-3 leading-relaxed">
                        Pilih peran pengamat demo untuk langsung menjelajahi dashboard laba bersih atau layar kasir POS:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <form method="POST" action="{{ route('login.demo') }}">
                            @csrf
                            <input type="hidden" name="role" value="owner">
                            <button type="submit" class="btn-primary w-full py-2.5 px-3 text-xs font-extrabold text-center bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-xs">
                                Mode Pemilik &rarr;
                            </button>
                        </form>
                        <form method="POST" action="{{ route('login.demo') }}">
                            @csrf
                            <input type="hidden" name="role" value="kasir">
                            <button type="submit" class="btn-ghost w-full py-2.5 px-3 text-xs font-bold text-center border-slate-300 bg-white hover:bg-slate-100 text-slate-800 shadow-xs">
                                Mode Kasir &rarr;
                            </button>
                        </form>
                    </div>
                </div>

                <div class="relative flex items-center justify-center my-5 sm:my-6">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-white px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 absolute">Atau Masuk Akun Pribadi</span>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label" for="email">Alamat Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input" placeholder="owner@umkm.test">
                    </div>

                    <div>
                        <label class="label" for="password">Kata Sandi</label>
                        <input id="password" name="password" type="password" required class="input" placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 font-medium text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button class="btn-primary w-full py-3 text-sm font-bold mt-2">
                        Masuk ke Dashboard &rarr;
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-100 pt-4 text-center">
                    <p class="text-xs text-slate-500">
                        Butuh bantuan setup atau konsultasi toko?
                        <a href="mailto:jaddadeveloper@gmail.com?subject=Bantuan%20Aplikasi%20Keuangan%20UMKM" class="font-bold text-amber-700 hover:underline block sm:inline mt-1 sm:mt-0">
                            jaddadeveloper@gmail.com ✉️
                        </a>
                    </p>
                </div>
            </div>

            {{-- Mobile footer credit --}}
            <div class="mt-6 text-center text-[11px] leading-relaxed text-slate-400 lg:hidden">
                <p>Dikembangkan oleh <strong class="text-slate-700">bipi (jaddadeveloper)</strong></p>
                <p>Open source · Lisensi MIT</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

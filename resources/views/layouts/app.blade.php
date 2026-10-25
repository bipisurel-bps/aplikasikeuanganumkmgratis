<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-800 antialiased selection:bg-amber-100 selection:text-amber-900">
@php
    $user = auth()->user();
    $openDrawer = $user->openDrawer();
    $nav = collect([
        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Laporan & Laba', 'owner' => true, 'icon' => 'M3 3v18h18M7 14l4-4 4 4 5-6'],
        ['route' => 'pos', 'match' => 'pos', 'label' => 'Kasir (POS)', 'owner' => false, 'icon' => 'M3 3h2l.4 2M7 13h10l3-8H5.4M7 13L5.4 5M7 13l-2 4h13M9 21a1 1 0 100-2 1 1 0 000 2zm9 0a1 1 0 100-2 1 1 0 000 2z'],
        ['route' => 'sales.index', 'match' => 'sales.*', 'label' => 'Riwayat Penjualan', 'owner' => false, 'icon' => 'M9 5h6M9 9h6m-6 4h4M5 3h14a1 1 0 011 1v16l-3-2-3 2-3-2-3 2-3-2V4a1 1 0 011-1z'],
        ['route' => 'drawers.index', 'match' => 'drawers.*', 'label' => 'Shift Kasir', 'owner' => false, 'icon' => 'M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => 'products.index', 'match' => 'products.*', 'label' => 'Katalog Produk & Stok', 'owner' => true, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'purchases.index', 'match' => 'purchases.*', 'label' => 'Restock Supplier (HPP)', 'owner' => true, 'icon' => 'M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01'],
        ['route' => 'suppliers.index', 'match' => 'suppliers.*', 'label' => 'Daftar Supplier', 'owner' => true, 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0'],
        ['route' => 'expenses.index', 'match' => 'expenses.*', 'label' => 'Beban & Anggaran', 'owner' => true, 'icon' => 'M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => 'cash.index', 'match' => 'cash.*', 'label' => 'Buku Kas Terpadu', 'owner' => true, 'icon' => 'M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zm3 8h2'],
    ])->filter(fn ($i) => ! $i['owner'] || $user->isOwner());
@endphp

<div x-data="{ sidebarOpen: false }" class="flex h-full">
    {{-- Mobile backdrop --}}
    <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 flex w-68 flex-col border-r border-slate-200/90 bg-white transition-transform duration-200 lg:static lg:z-auto">
        {{-- Brand Header --}}
        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-4.5">
            <div class="relative shrink-0">
                @include('partials.logo', ['class' => 'h-10 w-10'])
                <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-extrabold tracking-tight text-slate-950">{{ config('umkm.store_name') }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    <p class="truncate text-[10.5px] font-semibold tracking-wide text-slate-500">Sistem Kasir &amp; Akuntansi</p>
                </div>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 pb-4">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}" @click="sidebarOpen = false"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition-all duration-150 {{ $active ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                    <svg class="h-4.5 w-4.5 shrink-0 transition {{ $active ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                    <span class="truncate">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- User Info & Developer Attribution --}}
        <div class="border-t border-slate-100 bg-slate-50/70 p-4">
            <div class="mb-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white uppercase">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-slate-900 leading-tight">{{ $user->name }}</p>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">{{ $user->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 shadow-xs hover:bg-slate-100 hover:text-rose-600" title="Keluar">
                        Keluar
                    </button>
                </form>
            </div>

            {{-- Signature Developer Credit --}}
            <div class="border-t border-slate-200/80 pt-3 text-[10.5px] leading-relaxed text-slate-500">
                <p>Dikembangkan oleh <strong class="font-bold text-slate-800">bipi (jaddadeveloper)</strong> untuk kemanfaatan bersama seluas-luasnya.</p>
                <p class="mt-0.5 text-[9.5px] font-medium text-slate-400">Open source · Lisensi MIT</p>
            </div>
        </div>
    </aside>

    {{-- Main Workspace --}}
    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
        {{-- Banner Notifikasi Mode Demo (Pengamat) --}}
        @if (session('is_demo'))
            <div class="z-40 flex items-center justify-between gap-2 border-b border-amber-400 bg-amber-400 px-3 sm:px-4 py-2 text-xs font-bold text-slate-950 shadow-xs">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="flex h-2 w-2 shrink-0 rounded-full bg-slate-950 animate-pulse"></span>
                    <span class="truncate sm:overflow-visible sm:whitespace-normal">
                        <strong>Mode Demo:</strong> <span class="hidden sm:inline">Anda bebas menjelajahi menu. Perubahan data dinonaktifkan untuk menjaga kestabilan data contoh.</span><span class="sm:hidden">Read-Only</span>
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="rounded-lg bg-slate-950 px-2.5 py-1 text-[11px] sm:text-xs font-extrabold text-amber-400 hover:bg-slate-900 transition">
                        Keluar &rarr;
                    </button>
                </form>
            </div>
        @endif

        {{-- Sticky Glassmorphic Topbar --}}
        <header class="sticky top-0 z-30 flex h-14 sm:h-16 shrink-0 items-center justify-between border-b border-slate-200/80 bg-white/90 px-3 sm:px-6 lg:px-8 backdrop-blur-md">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Buka Menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <nav class="flex items-center gap-1.5 sm:gap-2 text-xs font-semibold text-slate-400 min-w-0" aria-label="Breadcrumb">
                    <span class="hidden sm:inline hover:text-slate-600">Aplikasi Keuangan UMKM</span>
                    <span class="hidden sm:inline text-slate-300">/</span>
                    <span class="truncate font-bold text-slate-900">@yield('title')</span>
                </nav>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Shift Status Pill --}}
                @if ($openDrawer)
                    <a href="{{ route('drawers.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 sm:px-3 py-1 text-xs font-bold text-emerald-800 shadow-xs hover:bg-emerald-100 transition">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="hidden sm:inline">Shift Kasir Aktif</span>
                        <span class="font-mono text-emerald-700">@rp($openDrawer->opening_cash)</span>
                    </a>
                @else
                    <a href="{{ route('drawers.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs font-bold text-amber-800 shadow-xs hover:bg-amber-100 transition">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        <span class="hidden sm:inline">Shift Belum Dibuka</span>
                        <span class="sm:hidden">Shift Tutup</span>
                    </a>
                @endif

                <div class="hidden sm:block text-right border-l border-slate-200 pl-3">
                    <p class="font-mono text-[11px] font-semibold text-slate-600">{{ now()->translatedFormat('l, d M Y') }}</p>
                </div>
            </div>
        </header>
                    <p class="font-mono text-[11px] font-semibold text-slate-600">{{ now()->translatedFormat('l, d M Y') }}</p>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="@yield('container', 'mx-auto max-w-7xl p-4 sm:p-6 lg:p-8')">
                <div class="mb-6 hidden items-end justify-between lg:flex">
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-950">@yield('title')</h1>
                        @hasSection('subtitle')<p class="mt-1 text-xs font-medium text-slate-500">@yield('subtitle')</p>@endif
                    </div>
                    <div>@yield('actions')</div>
                </div>

                @include('partials.flash')
                @yield('content')
            </div>
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>

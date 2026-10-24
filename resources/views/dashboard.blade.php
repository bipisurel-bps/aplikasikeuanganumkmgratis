@extends('layouts.app')
@section('title', 'Laporan Keuangan & Laba Bersih')
@section('subtitle', 'Laba Bersih Riil = Omzet Penjualan − HPP (Harga Pokok Penjualan) − Beban Operasional')

@section('content')
@php
    $maxOmzet = max(1, collect($series)->max('omzet'));
    $laba = $summary['laba_bersih'];
    $presets = [
        'Bulan Ini' => [now()->startOfMonth(), now()],
        'Bulan Lalu' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
        '7 Hari Terakhir' => [now()->subDays(6), now()],
        '30 Hari Terakhir' => [now()->subDays(29), now()],
    ];
@endphp

{{-- Period Filter Toolbar --}}
<div class="mb-6 flex flex-wrap items-end justify-between gap-4 card p-4 border-slate-200">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="label" for="from">Dari Tanggal</label>
            <input id="from" type="date" name="from" value="{{ $from->toDateString() }}" class="input py-2 w-auto font-mono text-xs">
        </div>
        <div>
            <label class="label" for="to">Sampai Tanggal</label>
            <input id="to" type="date" name="to" value="{{ $to->toDateString() }}" class="input py-2 w-auto font-mono text-xs">
        </div>
        <button class="btn-primary py-2 text-xs">Terapkan Periode</button>
    </form>

    <div class="flex flex-wrap items-center gap-1.5">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Pintasan:</span>
        @foreach ($presets as $label => [$a, $b])
            @php $isSelected = ($from->toDateString() === $a->toDateString() && $to->toDateString() === $b->toDateString()); @endphp
            <a href="{{ route('dashboard', ['from' => $a->toDateString(), 'to' => $b->toDateString()]) }}"
               class="rounded-lg px-2.5 py-1.5 text-xs font-semibold transition {{ $isSelected ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Top 4 KPI Metrics Cards --}}
<div class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
    {{-- Omzet --}}
    <div class="card p-5 border-slate-200/90 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <span class="label mb-0">Total Omzet</span>
            <span class="badge bg-slate-100 text-slate-700 font-mono">{{ $summary['transaksi'] }} trx</span>
        </div>
        <p class="font-mono text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">@rp($summary['omzet'])</p>
        <p class="mt-2 text-[11px] font-medium text-slate-500">Gross Revenue periode ini</p>
    </div>

    {{-- HPP --}}
    <div class="card p-5 border-slate-200/90 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <span class="label mb-0">HPP Terjual</span>
            <span class="badge bg-amber-50 text-amber-700 border border-amber-200/60 font-mono">Moving Avg</span>
        </div>
        <p class="font-mono text-2xl font-extrabold tracking-tight text-amber-600 sm:text-3xl">@rp($summary['hpp'])</p>
        <p class="mt-2 text-[11px] font-medium text-slate-500">Laba kotor: <b class="font-mono text-slate-800">@rp($summary['laba_kotor'])</b></p>
    </div>

    {{-- Beban --}}
    <div class="card p-5 border-slate-200/90 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <span class="label mb-0">Beban Operasional</span>
            <span class="badge bg-rose-50 text-rose-700 border border-rose-200/60 font-mono">OPEX</span>
        </div>
        <p class="font-mono text-2xl font-extrabold tracking-tight text-rose-600 sm:text-3xl">@rp($summary['beban'])</p>
        <p class="mt-2 text-[11px] font-medium text-slate-500">Biaya sewa, gaji, listrik, dll.</p>
    </div>

    {{-- Laba Bersih Riil (Hero Card) --}}
    <div class="card p-5 border-0 {{ $laba >= 0 ? 'bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white ring-1 ring-amber-400/30' : 'bg-gradient-to-br from-rose-950 via-rose-900 to-slate-950 text-white' }} shadow-lg relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Laba Bersih Riil</span>
            <span class="badge bg-white/10 text-white font-mono text-[10px]">
                {{ $summary['omzet'] > 0 ? round($laba / $summary['omzet'] * 100, 1) : 0 }}% margin
            </span>
        </div>
        <p class="font-mono text-2xl font-black tracking-tight text-amber-300 sm:text-3xl">
            {{ $laba < 0 ? '-' : '' }}@rp(abs($laba))
        </p>
        <p class="mt-2 text-[11px] font-medium text-slate-300">
            Hasil bersih setelah HPP &amp; beban
        </p>
    </div>
</div>

{{-- Secondary Stats Row --}}
<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <a href="{{ route('cash.index') }}" class="card p-4.5 hover:border-slate-950 transition group">
        <div class="flex items-center justify-between">
            <span class="label mb-0">Saldo Kas Saat Ini</span>
            <span class="text-xs text-slate-400 group-hover:text-slate-900">&rarr;</span>
        </div>
        <p class="font-mono text-xl font-bold text-slate-900 mt-1">@rp($summary['saldo_kas'])</p>
        <p class="text-[11px] text-slate-500 mt-1">
            Masuk <span class="font-mono text-emerald-600">@rp($summary['cash_in'])</span> · Keluar <span class="font-mono text-rose-600">@rp($summary['cash_out'])</span>
        </p>
    </a>

    <a href="{{ route('products.index', ['low' => 1]) }}" class="card p-4.5 hover:border-slate-950 transition group">
        <div class="flex items-center justify-between">
            <span class="label mb-0">Peringatan Stok Menipis</span>
            <span class="text-xs text-slate-400 group-hover:text-slate-900">&rarr;</span>
        </div>
        <p class="font-mono text-xl font-bold {{ $summary['low_stock'] ? 'text-amber-600' : 'text-slate-900' }} mt-1">
            {{ $summary['low_stock'] }} Produk
        </p>
        <p class="text-[11px] text-slate-500 mt-1">Stok &le; batas minimum toko</p>
    </a>

    <a href="{{ route('expenses.index', ['period' => $budgetPeriod]) }}" class="card p-4.5 hover:border-slate-950 transition group">
        <div class="flex items-center justify-between">
            <span class="label mb-0">Penyerapan Anggaran</span>
            <span class="text-xs text-slate-400 group-hover:text-slate-900">&rarr;</span>
        </div>
        @php $tb = collect($budgets)->sum('budget'); $ts = collect($budgets)->sum('spent'); @endphp
        <p class="font-mono text-xl font-bold text-slate-900 mt-1">
            {{ $tb > 0 ? round($ts / $tb * 100) . '%' : '—' }}
        </p>
        <p class="text-[11px] text-slate-500 mt-1">
            <span class="font-mono">@rp($ts)</span> dari pagu <span class="font-mono">@rp($tb)</span>
        </p>
    </a>
</div>

{{-- Main Grid: Daily Chart & Top Products --}}
<div class="grid gap-6 lg:grid-cols-3">
    {{-- Daily Revenue Chart --}}
    <section class="card p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-extrabold text-slate-950">Grafik Omzet Harian</h2>
                <p class="text-xs text-slate-500">Pergerakan transaksi penjualan dari hari ke hari</p>
            </div>
            <span class="badge bg-slate-100 text-slate-700 font-mono text-[11px]">Puncak: @rp($maxOmzet)</span>
        </div>

        <div class="flex h-52 items-end gap-1.5 overflow-x-auto pb-2 border-b border-slate-100">
            @foreach ($series as $d)
                @php $barHeight = max(2, round($d['omzet'] / $maxOmzet * 100)); @endphp
                <div class="group relative flex h-full min-w-[14px] flex-1 flex-col justify-end items-center"
                     title="{{ $d['label'] }} ({{ $d['date'] }}): Omzet Rp {{ number_format($d['omzet'], 0, ',', '.') }} | Laba Kotor Rp {{ number_format($d['laba_kotor'], 0, ',', '.') }}">
                    {{-- Tooltip hover chip --}}
                    <div class="absolute -top-8 hidden group-hover:flex z-20 whitespace-nowrap rounded-md bg-slate-950 px-2 py-1 text-[10px] font-mono text-white shadow-md">
                        {{ $d['label'] }}: Rp {{ number_format($d['omzet'], 0, ',', '.') }}
                    </div>
                    <div class="w-full rounded-t-md bg-slate-900/80 transition-all duration-150 group-hover:bg-amber-500"
                         style="height: {{ $barHeight }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="mt-3 flex justify-between font-mono text-[11px] text-slate-400">
            <span>{{ $series[0]['label'] ?? '' }} (Awal Periode)</span>
            <span>{{ end($series)['label'] ?? '' }} (Akhir Periode)</span>
        </div>
    </section>

    {{-- Top Products Leaderboard --}}
    <section class="card p-6">
        <div class="mb-4">
            <h2 class="text-base font-extrabold text-slate-950">Produk Terlaris</h2>
            <p class="text-xs text-slate-500">Volume penjualan tertinggi periode ini</p>
        </div>

        <ol class="space-y-3.5">
            @forelse ($topProducts as $i => $p)
                @php
                    $rankBadge = match ($i) {
                        0 => 'bg-amber-400 text-slate-950 ring-2 ring-amber-200',
                        1 => 'bg-slate-200 text-slate-800',
                        2 => 'bg-amber-700/20 text-amber-800',
                        default => 'bg-slate-100 text-slate-600',
                    };
                @endphp
                <li class="flex items-center gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-black {{ $rankBadge }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-slate-900">{{ $p->name }}</p>
                        <p class="font-mono text-[11px] text-slate-500">
                            {{ $p->qty }} terjual · Laba <span class="text-emerald-700 font-semibold">@rp($p->profit)</span>
                        </p>
                    </div>
                    <p class="font-mono text-xs font-bold text-slate-900">@rp($p->revenue)</p>
                </li>
            @empty
                <li class="py-8 text-center text-xs text-slate-400">Belum ada data transaksi.</li>
            @endforelse
        </ol>
    </section>

    {{-- Budget Progress Section --}}
    <section class="card p-6 lg:col-span-3">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-950">
                    Pemantauan Anggaran Beban ({{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $budgetPeriod)->translatedFormat('F Y') }})
                </h2>
                <p class="text-xs text-slate-500">Realisasi beban terhadap plafon anggaran bulanan per kategori</p>
            </div>
            <a href="{{ route('expenses.index', ['period' => $budgetPeriod]) }}" class="btn-ghost py-1.5 px-3 text-xs font-bold">
                Kelola Anggaran &rarr;
            </a>
        </div>
        <div class="grid gap-x-8 gap-y-4 md:grid-cols-2">
            @include('partials.budget-bars')
        </div>
    </section>
</div>
@endsection

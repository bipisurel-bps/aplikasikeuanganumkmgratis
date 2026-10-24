@extends('layouts.app')
@section('title', 'Manajemen Shift Kasir & Laci Kas')
@section('subtitle', 'Buka dan tutup laci kas, rekonsiliasi uang fisik di laci, dan jejak selisih kasir.')

@section('content')
@if ($current)
    <div class="card mb-6 border-slate-300 p-6 shadow-md" x-data="{ v: {{ (int) $currentExpected }} }">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="flex h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <div>
                    <h2 class="text-base font-extrabold text-slate-950">Shift Kasir Sedang Berjalan</h2>
                    <p class="text-xs text-slate-500">
                        Dibuka pada {{ $current->opened_at->translatedFormat('d M Y H:i') }} · Modal Awal <strong class="font-mono text-slate-800">@rp($current->opening_cash)</strong>
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="label mb-0">Uang Tunai Seharusnya di Laci</p>
                <p class="font-mono text-2xl font-black text-slate-950">@rp($currentExpected)</p>
                <p class="text-[10px] text-slate-400 font-medium">Modal Awal + Total Penjualan Tunai Shift Ini</p>
            </div>
        </div>

        <form method="POST" action="{{ route('drawers.close', $current) }}" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            @csrf
            <div>
                <label class="label" for="closing_cash">Jumlah Uang Fisik Hasil Hitung (Rp)</label>
                <input id="closing_cash" name="closing_cash" type="number" min="0" x-model.number="v" required class="input font-mono text-sm font-bold">
            </div>
            <div>
                <label class="label" for="note">Catatan / Alasan Selisih (Opsional)</label>
                <input id="note" name="note" class="input text-xs" placeholder="mis. Kembalian lebih / pecahan habis…">
            </div>
            <button class="btn-danger py-2.5 text-xs font-bold" onclick="return confirm('Apakah Anda yakin ingin menutup shift kasir ini sekarang?')">
                Tutup Shift &amp; Hitung Selisih
            </button>
        </form>
    </div>
@else
    <form method="POST" action="{{ route('drawers.open') }}" class="card mb-6 flex flex-wrap items-end gap-3.5 p-5 border-slate-200">
        @csrf
        <div class="min-w-[14rem] flex-1">
            <label class="label" for="opening_cash">Modal Awal Laci Kasir (Rp)</label>
            <input id="opening_cash" name="opening_cash" type="number" min="0" value="200000" required class="input font-mono text-sm font-bold">
        </div>
        <button class="btn-primary text-xs py-2.5 font-bold">
            Buka Shift Kasir Baru
        </button>
    </form>
@endif

<div class="card overflow-hidden border-slate-200">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Riwayat Pembukaan Shift &amp; Rekonsiliasi Kas</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="th">Kasir Bertugas</th>
                    <th class="th">Jam Buka</th>
                    <th class="th">Jam Tutup</th>
                    <th class="th text-right">Modal Awal</th>
                    <th class="th text-right">Seharusnya</th>
                    <th class="th text-right">Fisik Dihitung</th>
                    <th class="th text-right">Status Selisih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($drawers as $d)
                <tr class="hover:bg-slate-50 transition">
                    <td class="td font-bold text-xs text-slate-900">{{ $d->user->name }}</td>
                    <td class="td whitespace-nowrap font-mono text-xs">{{ $d->opened_at->translatedFormat('d M H:i') }}</td>
                    <td class="td whitespace-nowrap font-mono text-xs">{{ $d->closed_at?->translatedFormat('d M H:i') ?? '—' }}</td>
                    <td class="td text-right font-mono text-xs text-slate-600">@rp($d->opening_cash)</td>
                    <td class="td text-right font-mono text-xs text-slate-600">
                        @if ($d->expected_cash !== null) @rp($d->expected_cash) @else — @endif
                    </td>
                    <td class="td text-right font-mono text-xs font-bold text-slate-950">
                        @if ($d->closing_cash !== null) @rp($d->closing_cash) @else — @endif
                    </td>
                    <td class="td text-right font-mono text-xs font-black">
                        @if ($d->difference === null)
                            <span class="badge bg-emerald-50 text-emerald-800 border border-emerald-200">Aktif</span>
                        @elseif ($d->difference === 0)
                            <span class="badge bg-slate-100 text-slate-700">Pas (0)</span>
                        @elseif ($d->difference > 0)
                            <span class="text-amber-700 font-bold">+@rp($d->difference)</span>
                        @else
                            <span class="text-rose-600 font-bold">-@rp(abs($d->difference))</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="td py-12 text-center text-xs text-slate-400">
                        Belum ada riwayat shift kasir.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $drawers->links() }}</div>
@endsection

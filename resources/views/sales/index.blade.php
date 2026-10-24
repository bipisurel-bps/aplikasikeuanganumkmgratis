@extends('layouts.app')
@section('title', 'Riwayat Transaksi Penjualan')
@section('subtitle', auth()->user()->isOwner() ? 'Seluruh riwayat struk penjualan toko' : 'Transaksi yang diproses oleh kasir Anda')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nomor invoice (INV-...)…" class="input max-w-xs text-xs font-mono">
        <button class="btn-ghost py-2 text-xs">Cari</button>
    </form>
    <span class="badge bg-slate-100 text-slate-700 font-mono text-[11px]">{{ $sales->total() }} Transaksi</span>
</div>

<div class="card overflow-hidden border-slate-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="th">No. Invoice</th>
                    <th class="th">Waktu Transaksi</th>
                    <th class="th">Kasir</th>
                    <th class="th">Metode Bayar</th>
                    <th class="th text-right">Item</th>
                    <th class="th text-right">Total Belanja</th>
                    @if (auth()->user()->isOwner())<th class="th text-right">Laba Kotor</th>@endif
                    <th class="th text-right">Struk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($sales as $s)
                <tr class="hover:bg-slate-50 transition">
                    <td class="td font-mono text-xs font-bold text-slate-950">{{ $s->invoice_no }}</td>
                    <td class="td whitespace-nowrap font-mono text-xs">{{ $s->sold_at->translatedFormat('d M Y H:i') }}</td>
                    <td class="td text-xs font-semibold text-slate-800">{{ $s->user->name }}</td>
                    <td class="td">
                        <span class="badge bg-slate-100 text-slate-800 font-mono text-[10.5px]">
                            {{ config('umkm.payment_methods')[$s->payment_method] ?? $s->payment_method }}
                        </span>
                    </td>
                    <td class="td text-right font-mono text-xs">{{ $s->items_count }}</td>
                    <td class="td text-right font-mono text-xs font-black text-slate-950">@rp($s->total)</td>
                    @if (auth()->user()->isOwner())
                        <td class="td text-right font-mono text-xs font-bold text-emerald-800">@rp($s->profit)</td>
                    @endif
                    <td class="td text-right">
                        <a href="{{ route('sales.show', $s) }}" class="font-bold text-xs text-slate-900 hover:underline">
                            Lihat &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="td py-12 text-center text-xs text-slate-400">
                        Belum ada riwayat transaksi penjualan.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $sales->links() }}</div>
@endsection

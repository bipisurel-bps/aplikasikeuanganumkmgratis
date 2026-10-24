@extends('layouts.app')
@section('title', 'Buku Kas Terpadu & Mutasi')
@section('subtitle', 'Jurnal otomatis arus kas: penerimaan penjualan kasir, pembayaran restock supplier, dan pengeluaran beban operasional.')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-4">
    <div class="card px-6 py-4.5 border-slate-200">
        <p class="label mb-0">Saldo Kas Bersih Saat Ini</p>
        <p class="font-mono text-3xl font-black {{ $balance < 0 ? 'text-rose-600' : 'text-slate-950' }} mt-1">
            @rp($balance)
        </p>
    </div>

    <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200/90 shadow-xs">
        @foreach ([null => 'Semua Mutasi', 'in' => 'Hanya Kas Masuk', 'out' => 'Hanya Kas Keluar'] as $k => $l)
            <a href="{{ route('cash.index', $k ? ['direction' => $k] : []) }}"
               class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ request('direction') == $k ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                {{ $l }}
            </a>
        @endforeach
    </div>
</div>

<div class="card overflow-hidden border-slate-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="th">Waktu Mutasi</th>
                    <th class="th">Uraian / Deskripsi Jurnal</th>
                    <th class="th">Metode</th>
                    <th class="th text-right">Kas Masuk (Debit)</th>
                    <th class="th text-right">Kas Keluar (Kredit)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($mutations as $m)
                <tr class="hover:bg-slate-50 transition">
                    <td class="td whitespace-nowrap font-mono text-xs">{{ $m->occurred_at->translatedFormat('d M Y H:i') }}</td>
                    <td class="td">
                        <p class="font-bold text-xs text-slate-950">{{ $m->description }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">{{ $m->source_type ? strtoupper($m->source_type) : 'MANUAL' }}</p>
                    </td>
                    <td class="td">
                        <span class="badge bg-slate-100 text-slate-700 font-mono text-[10px]">
                            {{ config('umkm.payment_methods')[$m->method] ?? $m->method }}
                        </span>
                    </td>
                    <td class="td text-right font-mono text-xs font-black text-emerald-800">
                        {{ $m->direction === 'in' ? '+ Rp '.number_format($m->amount, 0, ',', '.') : '' }}
                    </td>
                    <td class="td text-right font-mono text-xs font-black text-rose-600">
                        {{ $m->direction === 'out' ? '- Rp '.number_format($m->amount, 0, ',', '.') : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="td py-12 text-center text-xs text-slate-400">
                        Belum ada riwayat mutasi kas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $mutations->links() }}</div>
@endsection

@extends('layouts.app')
@section('title', 'Restock & Pembelian Supplier')
@section('subtitle', 'Pencatatan faktur barang masuk dari supplier. HPP produk diperbarui otomatis dengan moving average.')
@section('actions')
    <a href="{{ route('purchases.create') }}" class="btn-primary text-xs py-2">+ Faktur Restock Baru</a>
@endsection

@section('content')
<div class="mb-4 lg:hidden">
    <a href="{{ route('purchases.create') }}" class="btn-primary w-full text-xs py-2.5">+ Faktur Restock Baru</a>
</div>

<div class="card overflow-hidden border-slate-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="th">No. Referensi (PO)</th>
                    <th class="th">Tanggal Faktur</th>
                    <th class="th">Nama Supplier</th>
                    <th class="th">Metode Bayar</th>
                    <th class="th text-right">Variasi Item</th>
                    <th class="th text-right">Total Belanja</th>
                    <th class="th text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($purchases as $p)
                <tr class="hover:bg-slate-50 transition">
                    <td class="td font-mono text-xs font-bold text-slate-950">{{ $p->reference_no }}</td>
                    <td class="td whitespace-nowrap font-mono text-xs">{{ $p->purchased_on->translatedFormat('d M Y') }}</td>
                    <td class="td">
                        <span class="font-bold text-xs text-slate-900">{{ $p->supplier?->name ?? '— Tanpa Supplier —' }}</span>
                    </td>
                    <td class="td">
                        <span class="badge bg-slate-100 text-slate-700 font-mono text-[10.5px]">
                            {{ config('umkm.payment_methods')[$p->payment_method] ?? $p->payment_method }}
                        </span>
                    </td>
                    <td class="td text-right font-mono text-xs">{{ $p->items_count }} jenis</td>
                    <td class="td text-right font-mono text-xs font-black text-slate-950">@rp($p->total)</td>
                    <td class="td text-right">
                        <a href="{{ route('purchases.show', $p) }}" class="font-bold text-xs text-slate-900 hover:underline">
                            Rincian &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="td py-12 text-center text-xs text-slate-400">
                        Belum ada riwayat restock barang yang tercatat.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $purchases->links() }}</div>
@endsection

@extends('layouts.app')
@section('title', 'Faktur Restock #'.$purchase->reference_no)
@section('actions')
    <a href="{{ route('purchases.index') }}" class="btn-ghost text-xs py-2">&larr; Kembali ke Daftar Restock</a>
@endsection

@section('content')
<div class="card mb-5 grid gap-4 p-6 sm:grid-cols-4 border-slate-200">
    <div>
        <p class="label">Tanggal Faktur</p>
        <p class="font-mono text-xs font-bold text-slate-950">{{ $purchase->purchased_on->translatedFormat('d F Y') }}</p>
    </div>
    <div>
        <p class="label">Nama Supplier</p>
        <p class="font-bold text-xs text-slate-950">{{ $purchase->supplier?->name ?? '— Pembelian Bebas —' }}</p>
    </div>
    <div>
        <p class="label">Dicatat Oleh</p>
        <p class="font-bold text-xs text-slate-950">{{ $purchase->user->name }}</p>
    </div>
    <div>
        <p class="label">Total Belanja</p>
        <p class="font-mono text-xl font-black text-slate-950">@rp($purchase->total)</p>
    </div>
    @if ($purchase->note)
        <div class="sm:col-span-4 border-t border-slate-100 pt-3">
            <p class="text-xs text-slate-500">Catatan: <strong class="text-slate-800">{{ $purchase->note }}</strong></p>
        </div>
    @endif
</div>

<div class="card overflow-hidden border-slate-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="th">Produk</th>
                    <th class="th text-right">Qty Restock</th>
                    <th class="th text-right">Harga Beli / Satuan</th>
                    <th class="th text-right">Subtotal Baris</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @foreach ($purchase->items as $i)
                <tr class="hover:bg-slate-50 transition">
                    <td class="td font-bold text-xs text-slate-900">{{ $i->product->name }}</td>
                    <td class="td text-right font-mono text-xs font-bold">{{ $i->qty }}</td>
                    <td class="td text-right font-mono text-xs text-slate-600">@rp($i->unit_cost)</td>
                    <td class="td text-right font-mono text-xs font-black text-slate-950">@rp($i->line_total)</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

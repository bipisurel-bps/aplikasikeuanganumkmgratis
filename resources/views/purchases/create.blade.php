@extends('layouts.app')
@section('title', 'Catat Faktur Restock Baru')
@section('subtitle', 'Stok bertambah dan formula moving average otomatis menghitung ulang HPP produk saat faktur disimpan.')
@section('actions')
    <a href="{{ route('purchases.index') }}" class="btn-ghost text-xs py-2">&larr; Kembali</a>
@endsection

@section('content')
<form method="POST" action="{{ route('purchases.store') }}" x-data="restock(@js($products))" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    @csrf
    <div class="card p-6 border-slate-200">
        <h2 class="text-sm font-extrabold text-slate-950 mb-3">Item Barang yang Dibeli</h2>

        <div class="mb-5 flex gap-2">
            <select x-model="pick" class="input text-xs">
                <option value="">Pilih produk yang ingin direstock…</option>
                <template x-for="p in products" :key="p.id">
                    <option :value="p.id" x-text="p.name + ' (Stok: ' + p.stock + ' | HPP: ' + rupiah(p.cost) + ')'"></option>
                </template>
            </select>
            <button type="button" @click="addPicked()" class="btn-primary shrink-0 text-xs font-bold">
                + Tambahkan
            </button>
        </div>

        <div class="space-y-3">
            <template x-for="(row, idx) in rows" :key="row.product_id">
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="mb-3 flex items-start justify-between gap-2">
                        <div>
                            <p class="font-bold text-slate-950 text-xs" x-text="row.name"></p>
                            <p class="font-mono text-[11px] text-slate-500">
                                Stok saat ini: <strong x-text="row.stock"></strong> · HPP lama: <span x-text="rupiah(row.cost)"></span>
                            </p>
                        </div>
                        <button type="button" @click="rows.splice(idx, 1)" class="rounded-lg p-1 text-slate-400 hover:bg-slate-200 hover:text-rose-600 leading-none" aria-label="Hapus">&times;</button>
                    </div>

                    <input type="hidden" :name="`items[${idx}][product_id]`" :value="row.product_id">
                    <div class="grid grid-cols-3 items-end gap-3">
                        <div>
                            <label class="label">Qty Masuk</label>
                            <input type="number" min="1" :name="`items[${idx}][qty]`" x-model.number="row.qty" required class="input font-mono text-xs font-bold">
                        </div>
                        <div>
                            <label class="label">Harga Beli Baru</label>
                            <input type="number" min="0" :name="`items[${idx}][unit_cost]`" x-model.number="row.unit_cost" required class="input font-mono text-xs font-bold">
                        </div>
                        <div class="text-right">
                            <p class="label">Subtotal Beli</p>
                            <p class="py-2.5 font-mono text-xs font-black text-slate-950" x-text="rupiah(row.qty * row.unit_cost)"></p>
                        </div>
                    </div>

                    <div class="mt-2.5 rounded-lg bg-white border border-slate-100 px-3 py-1.5 text-[11px] flex justify-between items-center">
                        <span class="text-slate-500">Estimasi HPP Rata-Rata Baru:</span>
                        <strong class="font-mono text-emerald-800" x-text="rupiah(newCost(row))"></strong>
                    </div>
                </div>
            </template>
            <div x-show="!rows.length" class="py-12 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                Belum ada produk yang dipilih. Silakan pilih produk dari dropdown di atas.
            </div>
        </div>
    </div>

    {{-- Restock Summary Card --}}
    <div class="card h-fit space-y-4 p-6 border-slate-200">
        <h2 class="text-sm font-extrabold text-slate-950 border-b border-slate-100 pb-2">Informasi Faktur</h2>

        <div>
            <label class="label">Pemasok / Supplier</label>
            <select name="supplier_id" class="input text-xs">
                <option value="">— Pembelian Bebas (Tanpa Supplier) —</option>
                @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>

        <div>
            <label class="label">Tanggal Faktur / Beli</label>
            <input type="date" name="purchased_on" value="{{ old('purchased_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="input font-mono text-xs">
        </div>

        <div>
            <label class="label">Metode Pembayaran Kas</label>
            <select name="payment_method" class="input text-xs">
                @foreach ($methods as $k => $l)<option value="{{ $k }}" @selected(old('payment_method') === $k)>{{ $l }}</option>@endforeach
            </select>
        </div>

        <div>
            <label class="label">Catatan Tambahan</label>
            <input name="note" value="{{ old('note') }}" class="input text-xs" placeholder="No. invoice supplier / memo…">
        </div>

        <div class="flex items-center justify-between border-t border-dashed border-slate-200 pt-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Pengeluaran</span>
            <span class="font-mono text-xl font-black text-slate-950" x-text="rupiah(total)"></span>
        </div>

        <button class="btn-primary w-full py-3.5 text-xs font-bold shadow-md" :disabled="!rows.length">
            Simpan Faktur &amp; Perbarui Stok &rarr;
        </button>
    </div>
</form>

@push('scripts')
<script>
function restock(products) {
    return {
        products, rows: [], pick: '',
        get total() { return this.rows.reduce((s, r) => s + (r.qty || 0) * (r.unit_cost || 0), 0); },
        addPicked() {
            const p = this.products.find(x => x.id == this.pick);
            if (!p || this.rows.some(r => r.product_id === p.id)) return;
            this.rows.push({ product_id: p.id, name: p.name, stock: p.stock, cost: p.cost, qty: 1, unit_cost: p.cost });
            this.pick = '';
        },
        newCost(r) {
            const t = r.stock + (r.qty || 0);
            return t > 0 ? (r.stock * r.cost + (r.qty || 0) * (r.unit_cost || 0)) / t : r.unit_cost;
        },
    };
}
</script>
@endpush
@endsection

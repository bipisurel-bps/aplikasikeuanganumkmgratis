@extends('layouts.app')
@section('title', 'Layar Kasir (POS)')
@section('container', 'p-3 sm:p-5 lg:p-6')

@section('content')
@if (! $drawer)
    <div class="mx-auto mt-8 max-w-md">
        <form method="POST" action="{{ route('drawers.open') }}" class="card space-y-5 p-7 border-slate-200 shadow-md">
            @csrf
            <div class="flex items-center gap-3.5">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/80">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-950">Buka Shift Kasir</h2>
                    <p class="text-xs text-slate-500">Hitung uang modal kasir di laci sebelum memulai penjualan.</p>
                </div>
            </div>

            <div x-data="{ v: '{{ old('opening_cash', 200000) }}' }">
                <label class="label" for="opening_cash">Modal Awal di Laci (Rp)</label>
                <div class="relative">
                    <input id="opening_cash" name="opening_cash" type="number" min="0" inputmode="numeric" x-model="v" required class="input font-mono text-lg font-bold pr-12">
                </div>
                <p class="mt-1.5 text-xs font-semibold text-slate-500" x-text="'Terbilang: ' + rupiah(v)"></p>
            </div>

            <button class="btn-primary w-full py-3.5 text-sm font-bold shadow-sm">
                Buka Shift &amp; Buka Meja Kasir &rarr;
            </button>
        </form>
    </div>
@else
<div x-data="pos(@js($products), @js($methods))" x-init="init()" class="lg:grid lg:h-[calc(100vh-5.5rem)] lg:grid-cols-[1fr_400px] lg:gap-5">
    {{-- Product Catalog Section --}}
    <section class="flex min-h-0 flex-col">
        {{-- Search & Scan Bar --}}
        <div class="mb-3.5 flex gap-2.5">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-4.3-4.3M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                <input x-ref="search" x-model="query" @keydown.enter.prevent="addFirstMatch()" type="search"
                       placeholder="Ketik nama produk atau scan barcode SKU lalu tekan Enter…"
                       class="input pl-10.5 pr-20 py-2.5 text-sm" autocomplete="off">
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-500">
                    ENTER
                </span>
            </div>
        </div>

        {{-- Categories Horizontal Scroll --}}
        <div class="mb-3.5 flex gap-2 overflow-x-auto pb-1 no-scrollbar">
            <button @click="category = null"
                    :class="category === null ? 'bg-slate-950 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-50'"
                    class="shrink-0 rounded-xl px-3.5 py-1.5 text-xs font-bold transition">
                Semua Produk (<span x-text="products.length"></span>)
            </button>
            @foreach ($categories as $c)
                <button @click="category = {{ $c->id }}"
                        :class="category === {{ $c->id }} ? 'bg-slate-950 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-50'"
                        class="shrink-0 rounded-xl px-3.5 py-1.5 text-xs font-bold transition">
                    {{ $c->name }}
                </button>
            @endforeach
        </div>

        {{-- Products Responsive Grid --}}
        <div class="grid min-h-0 flex-1 grid-cols-2 content-start gap-3 overflow-y-auto pb-24 sm:grid-cols-3 lg:pb-2 xl:grid-cols-4 pr-1">
            <template x-for="p in filtered" :key="p.id">
                <button @click="add(p)" :disabled="p.stock - qtyOf(p.id) <= 0"
                        class="card group flex flex-col p-3.5 text-left transition-all duration-150 hover:border-slate-950 hover:shadow-md active:scale-[.98] disabled:opacity-40 disabled:hover:border-slate-200 disabled:hover:shadow-none">
                    <div class="flex items-start justify-between gap-1 mb-1.5">
                        <span class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-text="p.sku"></span>
                        <span class="badge font-mono text-[10px]"
                              :class="p.stock - qtyOf(p.id) <= 5 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-slate-100 text-slate-600'">
                            Sisa <span x-text="p.stock - qtyOf(p.id)"></span>
                        </span>
                    </div>

                    <span class="line-clamp-2 min-h-[2.4rem] text-xs font-bold leading-snug text-slate-900 group-hover:text-slate-950" x-text="p.name"></span>

                    <div class="mt-2.5 flex items-baseline justify-between border-t border-slate-100 pt-2">
                        <span class="font-mono text-sm font-black text-slate-950" x-text="rupiah(p.price)"></span>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 rounded px-1.5 py-0.5" x-show="qtyOf(p.id) > 0" x-text="qtyOf(p.id) + ' di keranjang'"></span>
                    </div>
                </button>
            </template>
            <div x-show="filtered.length === 0" class="col-span-full py-16 text-center text-xs text-slate-400">
                Produk tidak ditemukan untuk kata kunci tersebut.
            </div>
        </div>
    </section>

    {{-- Mobile Floating Cart Bar --}}
    <button x-show="!cartOpen && cart.length" @click="cartOpen = true" x-cloak
            class="fixed inset-x-4 bottom-4 z-40 flex items-center justify-between rounded-2xl bg-slate-950 px-5 py-4 text-white shadow-2xl lg:hidden ring-1 ring-white/10">
        <div class="flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-400 text-xs font-black text-slate-950" x-text="itemCount"></span>
            <span class="text-xs font-bold text-slate-200">Lihat Keranjang</span>
        </div>
        <span class="font-mono text-base font-black text-amber-300" x-text="rupiah(total)"></span>
    </button>

    {{-- POS Cart Dock Sidebar --}}
    <aside :class="cartOpen ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'"
           class="card fixed inset-x-0 bottom-0 z-50 flex max-h-[90vh] min-h-0 flex-col rounded-b-none border-slate-200 transition-transform duration-200 lg:static lg:max-h-none lg:rounded-2xl shadow-lg">
        {{-- Cart Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-extrabold text-slate-950">Keranjang Kasir</h2>
                <span class="badge bg-slate-100 text-slate-700 font-mono text-[10px]" x-text="itemCount + ' item'"></span>
            </div>
            <div class="flex items-center gap-3">
                <button x-show="cart.length" @click="clear()" class="text-xs font-bold text-rose-600 hover:text-rose-700">
                    Kosongkan
                </button>
                <button @click="cartOpen = false" class="rounded-lg p-1 text-slate-400 hover:text-slate-600 lg:hidden" aria-label="Tutup">
                    &times;
                </button>
            </div>
        </div>

        {{-- Cart Line Items --}}
        <div class="min-h-0 flex-1 divide-y divide-slate-100 overflow-y-auto px-1">
            <template x-for="line in cart" :key="line.id">
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-slate-900" x-text="line.name"></p>
                        <p class="font-mono text-[11px] text-slate-500" x-text="rupiah(line.price)"></p>
                    </div>

                    {{-- Stepper Qty --}}
                    <div class="flex items-center gap-1">
                        <button @click="dec(line)" class="h-7 w-7 rounded-lg bg-slate-100 text-slate-700 font-black hover:bg-slate-200 text-xs">−</button>
                        <input type="number" min="1" :max="line.stock" x-model.number="line.qty" @change="clamp(line)"
                               class="h-7 w-10 rounded-lg border border-slate-200 font-mono text-center text-xs font-bold text-slate-900">
                        <button @click="inc(line)" class="h-7 w-7 rounded-lg bg-slate-100 text-slate-700 font-black hover:bg-slate-200 text-xs">+</button>
                    </div>

                    <p class="w-22 text-right font-mono text-xs font-black text-slate-950" x-text="rupiah(line.price * line.qty)"></p>
                </div>
            </template>
            <div x-show="!cart.length" class="px-6 py-16 text-center text-xs text-slate-400">
                Pilih atau scan produk di sebelah kiri untuk menambah ke transaksi.
            </div>
        </div>

        {{-- Payment Calculation & Checkout Form --}}
        <div class="space-y-3.5 border-t border-slate-100 bg-slate-50/50 p-4.5">
            <div class="space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Subtotal</span>
                    <span class="font-mono font-bold text-slate-800" x-text="rupiah(subtotal)"></span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <label for="discount" class="text-slate-500">Diskon Nota (Rp)</label>
                    <input id="discount" type="number" min="0" x-model.number="discount"
                           class="input w-32 py-1 px-2.5 text-right font-mono text-xs font-bold">
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-dashed border-slate-200 pt-2.5">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Tagihan</span>
                <span class="font-mono text-xl font-black text-slate-950" x-text="rupiah(total)"></span>
            </div>

            {{-- Payment Method Tabs --}}
            <div class="grid grid-cols-3 gap-1.5">
                @foreach ($methods as $key => $label)
                    <button type="button" @click="method = '{{ $key }}'"
                            :class="method === '{{ $key }}' ? 'border-slate-950 bg-slate-950 text-white shadow-xs' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                            class="rounded-xl border py-2 text-xs font-bold transition">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Cash Input & Quick Cash Buttons --}}
            <div x-show="method === 'cash'" class="space-y-2">
                <div class="relative">
                    <input type="number" min="0" inputmode="numeric" x-model.number="paid"
                           placeholder="Jumlah uang diterima"
                           class="input font-mono text-base font-bold text-slate-950 py-2">
                </div>

                {{-- Fast Change Pills --}}
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="paid = total" class="rounded-lg border border-slate-200 bg-white px-2 py-1 font-mono text-[10px] font-bold text-slate-700 hover:bg-slate-100">
                        Uang Pas
                    </button>
                    <template x-for="n in quickCash" :key="n">
                        <button type="button" @click="paid = n" class="rounded-lg border border-slate-200 bg-white px-2 py-1 font-mono text-[10px] font-bold text-slate-700 hover:bg-slate-100" x-text="rupiah(n)"></button>
                    </template>
                </div>

                <div class="flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold"
                     :class="change >= 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-rose-50 text-rose-700 border border-rose-200/60'">
                    <span x-text="change >= 0 ? 'Kembalian Tunai' : 'Uang Kurang'"></span>
                    <span class="font-mono text-sm font-black" x-text="rupiah(Math.abs(change))"></span>
                </div>
            </div>

            <p x-show="error" x-text="error" class="rounded-xl bg-rose-50 border border-rose-200 p-2.5 text-xs font-bold text-rose-700"></p>

            <button @click="submit()" :disabled="!canPay || busy"
                    class="btn-primary w-full py-3.5 text-sm font-extrabold shadow-md">
                <span x-text="busy ? 'Memproses Transaksi…' : 'Selesaikan Pembayaran (' + rupiah(total) + ')'"></span>
            </button>
        </div>
    </aside>

    {{-- Thermal Receipt Modal --}}
    <div x-cloak x-show="receipt" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/70 p-3 sm:items-center backdrop-blur-xs" @keydown.escape.window="newSale()">
        <div class="max-h-[95vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl border border-slate-200" @click.outside="">
            <template x-if="receipt">
                <div>
                    <div class="mb-4 flex items-center justify-center gap-2 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-extrabold">Transaksi Berhasil &amp; Tercatat</span>
                    </div>

                    @include('partials.receipt')

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <button @click="window.print()" class="btn-ghost font-bold text-xs py-2.5">
                            Cetak Struk
                        </button>
                        <button @click="newSale()" class="btn-primary font-bold text-xs py-2.5">
                            Transaksi Baru
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function pos(products, methods) {
    return {
        products, methods,
        cart: [], query: '', category: null, discount: 0, paid: 0, method: 'cash',
        cartOpen: false, busy: false, error: '', receipt: null,

        init() { this.$refs.search?.focus(); },

        get filtered() {
            const q = this.query.trim().toLowerCase();
            return this.products.filter(p =>
                (this.category === null || p.category_id === this.category) &&
                (!q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)));
        },
        get subtotal() { return this.cart.reduce((s, l) => s + l.price * l.qty, 0); },
        get itemCount() { return this.cart.reduce((s, l) => s + l.qty, 0); },
        get total() { return Math.max(0, this.subtotal - (Number(this.discount) || 0)); },
        get change() { return this.method === 'cash' ? (Number(this.paid) || 0) - this.total : 0; },
        get canPay() { return this.cart.length > 0 && this.change >= 0 && (Number(this.discount) || 0) <= this.subtotal; },
        get quickCash() {
            const t = this.total; if (!t) return [];
            const set = new Set([Math.ceil(t / 5000) * 5000, Math.ceil(t / 10000) * 10000, Math.ceil(t / 50000) * 50000, Math.ceil(t / 100000) * 100000]);
            return [...set].filter(n => n > t).slice(0, 3);
        },

        qtyOf(id) { return this.cart.find(l => l.id === id)?.qty ?? 0; },
        add(p) {
            const line = this.cart.find(l => l.id === p.id);
            if (line) { this.inc(line); return; }
            if (p.stock > 0) this.cart.push({ id: p.id, name: p.name, price: p.price, stock: p.stock, qty: 1 });
        },
        addFirstMatch() {
            const exact = this.products.find(p => p.sku.toLowerCase() === this.query.trim().toLowerCase());
            const hit = exact ?? (this.filtered.length === 1 ? this.filtered[0] : null);
            if (hit) { this.add(hit); this.query = ''; }
        },
        inc(line) { if (line.qty < line.stock) line.qty++; },
        dec(line) { line.qty--; if (line.qty < 1) this.cart = this.cart.filter(l => l !== line); },
        clamp(line) {
            line.qty = Math.floor(Number(line.qty) || 1);
            if (line.qty > line.stock) line.qty = line.stock;
            if (line.qty < 1) this.cart = this.cart.filter(l => l !== line);
        },
        clear() { this.cart = []; this.discount = 0; this.paid = 0; this.error = ''; },

        async submit() {
            if (!this.canPay || this.busy) return;
            this.busy = true; this.error = '';
            try {
                const res = await fetch(@js(route('pos.checkout')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        items: this.cart.map(l => ({ product_id: l.id, qty: l.qty })),
                        discount: Number(this.discount) || 0,
                        paid: this.method === 'cash' ? Number(this.paid) || 0 : this.total,
                        payment_method: this.method,
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.error = data.errors ? Object.values(data.errors).flat()[0] : (data.message || 'Transaksi gagal diproses.');
                    return;
                }
                this.cart.forEach(l => { const p = this.products.find(x => x.id === l.id); if (p) p.stock -= l.qty; });
                this.receipt = data.sale;
                this.cartOpen = false;
            } catch (e) {
                this.error = 'Koneksi bermasalah. Coba lagi.';
            } finally {
                this.busy = false;
            }
        },
        newSale() { this.receipt = null; this.clear(); this.$nextTick(() => this.$refs.search?.focus()); },
    };
}
</script>
@endpush
@endif
@endsection

@extends('layouts.app')
@section('title', 'Katalog Produk & Stok')
@section('subtitle', 'Daftar produk, SKU barcode, batas minimum stok, dan HPP (Harga Pokok Penjualan) otomatis.')

@php
    $blank = ['id' => null, 'sku' => '', 'name' => '', 'category_id' => '', 'stock' => 0, 'min_stock' => 5, 'cost_price' => 0, 'sell_price' => 0, 'is_active' => true];
@endphp

@section('content')
<div x-data="productPage()" x-init="init()">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-1 flex-wrap items-center gap-2.5">
            <input name="q" value="{{ request('q') }}" placeholder="Cari nama produk / SKU…" class="input max-w-xs text-xs">
            <select name="category" class="input w-auto text-xs" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }} ({{ $c->products_count }})</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 cursor-pointer shadow-xs">
                <input type="checkbox" name="low" value="1" @checked(request()->boolean('low')) onchange="this.form.submit()" class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
                <span>Hanya Stok Menipis</span>
            </label>
            <button class="btn-ghost py-2 text-xs">Filter</button>
        </form>

        <div class="flex items-center gap-2">
            <button @click="openCategories = true" class="btn-ghost text-xs py-2">
                Kelola Kategori
            </button>
            <button @click="openNew()" class="btn-primary text-xs py-2">
                + Tambah Produk Baru
            </button>
        </div>
    </div>

    <div class="card overflow-hidden border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr>
                        <th class="th">Produk &amp; SKU</th>
                        <th class="th">Kategori</th>
                        <th class="th text-right">Stok</th>
                        <th class="th text-right">HPP (Beli)</th>
                        <th class="th text-right">Harga Jual</th>
                        <th class="th text-right">Gross Margin</th>
                        <th class="th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($products as $p)
                    <tr class="{{ $p->is_active ? '' : 'opacity-40 bg-slate-50/50' }} hover:bg-slate-50/80 transition">
                        <td class="td">
                            <p class="font-bold text-slate-950 text-xs">{{ $p->name }}</p>
                            <p class="font-mono text-[11px] text-slate-500 uppercase">{{ $p->sku }}@unless($p->is_active) · <span class="text-rose-600 font-sans font-semibold">Nonaktif</span>@endunless</p>
                        </td>
                        <td class="td">
                            <span class="badge bg-slate-100 text-slate-700 text-[10.5px]">{{ $p->category?->name ?? '—' }}</span>
                        </td>
                        <td class="td text-right font-mono font-bold">
                            <span class="badge {{ $p->stock === 0 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : ($p->isLowStock() ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/60') }}">
                                {{ $p->stock }}
                            </span>
                        </td>
                        <td class="td text-right font-mono text-xs text-slate-600">@rp($p->cost_price)</td>
                        <td class="td text-right font-mono text-xs font-bold text-slate-950">@rp($p->sell_price)</td>
                        <td class="td text-right font-mono text-xs">
                            <span class="text-emerald-700 font-bold">@rp($p->margin)</span>
                            <span class="text-[10px] text-slate-400">({{ $p->sell_price > 0 ? round($p->margin / $p->sell_price * 100) : 0 }}%)</span>
                        </td>
                        <td class="td whitespace-nowrap text-right">
                            <button @click='openEdit(@json($p->only(array_keys($blank))))' class="font-bold text-xs text-slate-900 hover:underline">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('products.destroy', $p) }}" class="ml-3 inline" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button class="font-bold text-xs text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="td py-12 text-center text-xs text-slate-400">
                            Tidak ada produk yang sesuai dengan pencarian.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>

    {{-- Product Modal Form --}}
    <div x-cloak x-show="openForm" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/70 p-3 sm:items-center backdrop-blur-xs">
        <form method="POST" :action="action" @click.outside="openForm = false"
              class="max-h-[95vh] w-full max-w-lg space-y-4 overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
            @csrf
            <template x-if="f.id"><input type="hidden" name="_method" value="PUT"></template>
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-extrabold text-slate-950" x-text="f.id ? 'Ubah Informasi Produk' : 'Tambah Produk Baru'"></h2>
                <button type="button" @click="openForm = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label">SKU / Barcode Produk</label>
                    <input name="sku" x-model="f.sku" required class="input font-mono text-xs uppercase" placeholder="mis. MNM-001">
                </div>
                <div>
                    <label class="label">Kategori</label>
                    <select name="category_id" x-model="f.category_id" class="input text-xs">
                        <option value="">— Tanpa Kategori —</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="label">Nama Produk</label>
                <input name="name" x-model="f.name" required class="input text-xs" placeholder="mis. Air Mineral 600ml">
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label">Harga Pokok (HPP Beli Rp)</label>
                    <input name="cost_price" type="number" min="0" x-model.number="f.cost_price" required class="input font-mono text-xs">
                </div>
                <div>
                    <label class="label">Harga Jual Konsumen (Rp)</label>
                    <input name="sell_price" type="number" min="0" x-model.number="f.sell_price" required class="input font-mono text-xs font-bold">
                </div>
                <div>
                    <label class="label">Jumlah Stok Saat Ini</label>
                    <input name="stock" type="number" min="0" x-model.number="f.stock" required class="input font-mono text-xs">
                </div>
                <div>
                    <label class="label">Batas Minimum Peringatan</label>
                    <input name="min_stock" type="number" min="0" x-model.number="f.min_stock" required class="input font-mono text-xs">
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 text-xs" x-show="f.sell_price > 0">
                <span class="text-slate-500">Estimasi Gross Margin:</span>
                <strong class="font-mono text-emerald-800" x-text="rupiah(f.sell_price - f.cost_price)"></strong>
                <span class="font-mono text-slate-500" x-text="'(' + Math.round((f.sell_price - f.cost_price) / f.sell_price * 100) + '%)'"></span>
            </div>

            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" x-model="f.is_active" class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
                <span>Produk aktif dijual di kasir</span>
            </label>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="openForm = false" class="btn-ghost text-xs">Batal</button>
                <button class="btn-primary text-xs">Simpan Data Produk</button>
            </div>
        </form>
    </div>

    {{-- Category Management Modal --}}
    <div x-cloak x-show="openCategories" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/70 p-3 sm:items-center backdrop-blur-xs">
        <div @click.outside="openCategories = false" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-extrabold text-slate-950">Kelola Kategori</h2>
                <button type="button" @click="openCategories = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form method="POST" action="{{ route('categories.store') }}" class="flex gap-2">
                @csrf
                <input name="name" required placeholder="Nama kategori baru…" class="input text-xs">
                <button class="btn-primary shrink-0 text-xs font-bold">Tambah</button>
            </form>

            <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 border rounded-xl border-slate-100">
                @foreach ($categories as $c)
                    <div class="flex items-center justify-between p-2.5 text-xs">
                        <span class="font-semibold text-slate-800">{{ $c->name }} <span class="font-mono text-[10px] text-slate-400">({{ $c->products_count }})</span></span>
                        <form method="POST" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="font-bold text-rose-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <button @click="openCategories = false" class="btn-ghost w-full text-xs">Tutup Dialog</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function productPage() {
    const blank = @js($blank);
    return {
        f: { ...blank }, openForm: false, openCategories: false,
        get action() { return this.f.id ? @js(route('products.update', '__ID__')).replace('__ID__', this.f.id) : @js(route('products.store')); },
        openNew() { this.f = { ...blank }; this.openForm = true; },
        openEdit(p) { this.f = { ...p, category_id: p.category_id ?? '', is_active: !!p.is_active }; this.openForm = true; },
        init() { @if ($errors->any() && old('sku') !== null) this.f = { ...blank, ...@js(old()) }; this.openForm = true; @endif },
    };
}
</script>
@endpush
@endsection

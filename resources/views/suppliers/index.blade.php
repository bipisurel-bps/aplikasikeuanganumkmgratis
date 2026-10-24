@extends('layouts.app')
@section('title', 'Daftar Supplier & Pemasok')
@section('subtitle', 'Pemasok barang dagangan untuk pembelian restock toko')

@section('content')
<div x-data="{ editing: null, form: { name: '', phone: '', address: '' },
    edit(s) { this.editing = s.id; this.form = { name: s.name, phone: s.phone ?? '', address: s.address ?? '' }; },
    reset() { this.editing = null; this.form = { name: '', phone: '', address: '' }; },
    get action() { return this.editing ? @js(route('suppliers.update', '__ID__')).replace('__ID__', this.editing) : @js(route('suppliers.store')); } }"
     class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <div class="card overflow-hidden border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr>
                        <th class="th">Nama Pemasok</th>
                        <th class="th">Kontak Telepon</th>
                        <th class="th text-right">Frekuensi Restock</th>
                        <th class="th text-right">Total Transaksi</th>
                        <th class="th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($suppliers as $s)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="td">
                            <p class="font-bold text-xs text-slate-950">{{ $s->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $s->address ?: '— Alamat belum diisi —' }}</p>
                        </td>
                        <td class="td font-mono text-xs">{{ $s->phone ?: '—' }}</td>
                        <td class="td text-right font-mono text-xs font-semibold">{{ $s->purchases_count }}×</td>
                        <td class="td text-right font-mono text-xs font-black text-slate-950">@rp($s->purchases_sum_total ?? 0)</td>
                        <td class="td whitespace-nowrap text-right">
                            <button @click="edit(@js($s->only('id','name','phone','address')))" class="font-bold text-xs text-slate-900 hover:underline">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="ml-3 inline" onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf @method('DELETE')
                                <button class="font-bold text-xs text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="td py-12 text-center text-xs text-slate-400">
                            Belum ada supplier yang terdaftar.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Supplier Form Card --}}
    <form method="POST" :action="action" class="card h-fit space-y-4 p-6 border-slate-200 shadow-sm">
        @csrf
        <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
        <div class="border-b border-slate-100 pb-3">
            <h2 class="text-sm font-extrabold text-slate-950" x-text="editing ? 'Ubah Data Supplier' : 'Tambah Supplier Baru'"></h2>
            <p class="text-[11px] text-slate-500">Data ini akan tersedia saat mencatat faktur restock.</p>
        </div>

        <div>
            <label class="label">Nama Usaha / Pemasok</label>
            <input name="name" x-model="form.name" required class="input text-xs" placeholder="mis. CV Sumber Rezeki">
        </div>

        <div>
            <label class="label">Nomor WhatsApp / Telepon</label>
            <input name="phone" x-model="form.phone" class="input font-mono text-xs" placeholder="mis. 0812-3456-7890">
        </div>

        <div>
            <label class="label">Alamat Kantor / Gudang</label>
            <textarea name="address" x-model="form.address" rows="3" class="input text-xs" placeholder="mis. Pasar Induk Blok A No. 12"></textarea>
        </div>

        <div class="flex gap-2 pt-2">
            <button class="btn-primary flex-1 text-xs py-2.5">Simpan Data</button>
            <button type="button" x-show="editing" @click="reset()" class="btn-ghost text-xs py-2.5">Batal</button>
        </div>
    </form>
</div>
@endsection

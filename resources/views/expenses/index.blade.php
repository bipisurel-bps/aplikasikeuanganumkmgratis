@extends('layouts.app')
@section('title', 'Beban Operasional & Plafon Anggaran')
@section('subtitle', 'Pencatatan biaya harian (OPEX), bukti unggah nota, dan kontrol sisa anggaran bulanan — '.$periodLabel)

@section('content')
@php $remainingTotal = $totalBudget - $totalSpent; @endphp
<div x-data="{ openBudget: false, openForm: {{ $errors->any() && old('amount') !== null ? 'true' : 'false' }} }">
    {{-- Filter & Action Bar --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 card p-4 border-slate-200">
        <form method="GET" class="flex items-center gap-2.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider" for="period">Bulan Periode</label>
            <input id="period" type="month" name="period" value="{{ $period }}" onchange="this.form.submit()" class="input w-auto py-2 font-mono text-xs font-bold">
        </form>

        <div class="flex items-center gap-2">
            <button @click="openBudget = true" class="btn-ghost py-2 text-xs font-bold">
                Atur Plafon Anggaran
            </button>
            <button @click="openForm = true" class="btn-primary py-2 text-xs font-bold">
                + Catat Beban Operasional
            </button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[22rem_1fr]">
        {{-- Sisa Anggaran Panel --}}
        <section class="card h-fit p-5 border-slate-200">
            <h2 class="text-sm font-extrabold text-slate-950 mb-1">Status Anggaran {{ $periodLabel }}</h2>
            @if ($totalBudget > 0)
                <p class="font-mono text-3xl font-black {{ $remainingTotal < 0 ? 'text-rose-600' : 'text-slate-950' }}">
                    @rp($remainingTotal)
                </p>
                <p class="mt-1 mb-4 text-xs font-medium text-slate-500">
                    Sisa dari pagu total <strong class="font-mono text-slate-800">@rp($totalBudget)</strong> (terpakai <span class="font-mono">@rp($totalSpent)</span>)
                </p>
            @else
                <p class="mt-1 mb-4 text-xs font-medium text-slate-500">
                    Plafon anggaran belum diatur. Beban tercatat <strong class="font-mono text-slate-800">@rp($totalSpent)</strong>.
                </p>
            @endif

            <div class="space-y-3.5 border-t border-slate-100 pt-4">
                @include('partials.budget-bars')
            </div>
        </section>

        {{-- Daftar Beban Table --}}
        <section class="card overflow-hidden border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Jurnal Pengeluaran Operasional</h3>
                <span class="badge bg-slate-100 text-slate-700 font-mono text-[10px]">{{ $expenses->total() }} catatan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr>
                            <th class="th">Tanggal</th>
                            <th class="th">Kategori</th>
                            <th class="th">Keterangan Beban</th>
                            <th class="th text-right">Nominal (Rp)</th>
                            <th class="th text-center">Bukti Nota</th>
                            <th class="th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($expenses as $e)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="td whitespace-nowrap font-mono text-xs">{{ $e->spent_on->translatedFormat('d M Y') }}</td>
                            <td class="td">
                                <span class="badge bg-slate-100 text-slate-800 text-[10px]">{{ $e->category }}</span>
                            </td>
                            <td class="td">
                                <p class="font-bold text-slate-900 text-xs">{{ $e->description }}</p>
                                <p class="text-[10px] text-slate-400">Dicatat oleh {{ $e->user->name }}</p>
                            </td>
                            <td class="td text-right font-mono text-xs font-black text-rose-600">
                                @rp($e->amount)
                            </td>
                            <td class="td text-center">
                                @if ($e->receipt_path)
                                    <a href="{{ route('expenses.receipt', $e) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-xs text-slate-900 hover:underline">
                                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span>Lihat</span>
                                    </a>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="td text-right">
                                <form method="POST" action="{{ route('expenses.destroy', $e) }}" onsubmit="return confirm('Hapus pengeluaran ini? Jurnal mutasi kas terkait akan dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button class="font-bold text-xs text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="td py-12 text-center text-xs text-slate-400">
                                Belum ada pengeluaran operasional yang dicatat pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $expenses->links() }}</div>
        </section>
    </div>

    {{-- Expense Creation Modal --}}
    <div x-cloak x-show="openForm" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/70 p-3 sm:items-center backdrop-blur-xs">
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" @click.outside="openForm = false"
              class="max-h-[95vh] w-full max-w-md space-y-4 overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
            @csrf
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-extrabold text-slate-950">Catat Beban Operasional</h2>
                <button type="button" @click="openForm = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <div>
                <label class="label">Kategori Beban</label>
                <select name="category" required class="input text-xs">
                    @foreach ($categories as $c)<option @selected(old('category') === $c)>{{ $c }}</option>@endforeach
                </select>
            </div>

            <div>
                <label class="label">Keterangan / Rincian Pengeluaran</label>
                <input name="description" value="{{ old('description') }}" required class="input text-xs" placeholder="mis. Token listrik toko &amp; air galon">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Nominal Biaya (Rp)</label>
                    <input name="amount" type="number" min="1" value="{{ old('amount') }}" required class="input font-mono text-xs font-bold text-rose-700">
                </div>
                <div>
                    <label class="label">Tanggal Bayar</label>
                    <input name="spent_on" type="date" value="{{ old('spent_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="input font-mono text-xs">
                </div>
            </div>

            <div>
                <label class="label">Lampiran Bukti Nota / Kuitansi (Maks 4 MB)</label>
                <input name="receipt" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-bold file:text-slate-700 hover:file:bg-slate-200">
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="openForm = false" class="btn-ghost text-xs">Batal</button>
                <button class="btn-primary text-xs">Simpan Beban</button>
            </div>
        </form>
    </div>

    {{-- Budget Setting Modal --}}
    <div x-cloak x-show="openBudget" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/70 p-3 sm:items-center backdrop-blur-xs">
        <form method="POST" action="{{ route('budgets.store') }}" @click.outside="openBudget = false"
              class="max-h-[95vh] w-full max-w-md space-y-3.5 overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
            @csrf
            <input type="hidden" name="period" value="{{ $period }}">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-extrabold text-slate-950">Atur Plafon Anggaran</h2>
                    <p class="text-[11px] text-slate-500">Periode {{ $periodLabel }}</p>
                </div>
                <button type="button" @click="openBudget = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <p class="text-[11px] text-slate-500">Isi plafon maksimal untuk setiap pos beban. Kosongkan jika tidak dibatasi.</p>

            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                @foreach ($budgets as $b)
                    <div class="flex items-center justify-between gap-3 p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <label class="text-xs font-bold text-slate-800">{{ $b['category'] }}</label>
                        <input type="number" min="0" name="budgets[{{ $b['category'] }}]" value="{{ $b['budget'] ?: '' }}"
                               placeholder="0" class="input w-36 py-1 px-2.5 text-right font-mono text-xs font-bold">
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="openBudget = false" class="btn-ghost text-xs">Batal</button>
                <button class="btn-primary text-xs">Simpan Plafon Anggaran</button>
            </div>
        </form>
    </div>
</div>
@endsection

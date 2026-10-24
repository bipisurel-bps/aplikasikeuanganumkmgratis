@extends('layouts.app')
@section('title', 'Struk Belanja '.$receipt['invoice_no'])
@section('actions')
    <a href="{{ route('sales.index') }}" class="btn-ghost text-xs py-2">&larr; Kembali ke Riwayat</a>
@endsection
@section('container', 'mx-auto max-w-md p-4 sm:p-6')

@section('content')
<div x-data='{ receipt: @json($receipt) }'>
    <a href="{{ route('sales.index') }}" class="mb-4 inline-block text-xs font-bold text-slate-700 lg:hidden">&larr; Kembali ke Riwayat</a>
    @include('partials.receipt')
    <button @click="window.print()" class="btn-primary mt-4 w-full py-3 text-xs font-bold shadow-md">
        Cetak Struk Transaksi
    </button>
</div>
@endsection

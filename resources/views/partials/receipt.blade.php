{{-- High-End Thermal Receipt Struk Belanja --}}
<div class="print-area rounded-xl border border-dashed border-slate-300 bg-white p-5 font-mono text-xs text-slate-900 shadow-xs">
    <div class="text-center space-y-0.5">
        <p class="text-sm font-black tracking-tight uppercase" x-text="receipt.store.name"></p>
        <p class="text-[11px] text-slate-600" x-text="receipt.store.address"></p>
        <p class="text-[11px] text-slate-500" x-text="receipt.store.phone"></p>
    </div>

    <div class="my-3 border-t border-dashed border-slate-300"></div>

    <div class="space-y-1 text-[11px]">
        <div class="flex justify-between">
            <span class="text-slate-500">No. Invoice</span>
            <span class="font-bold" x-text="receipt.invoice_no"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Waktu</span>
            <span x-text="receipt.sold_at"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Kasir</span>
            <span class="font-semibold" x-text="receipt.cashier"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Metode Bayar</span>
            <span class="font-bold uppercase" x-text="receipt.method"></span>
        </div>
    </div>

    <div class="my-3 border-t border-dashed border-slate-300"></div>

    <div class="space-y-2">
        <template x-for="i in receipt.items" :key="i.name">
            <div>
                <p class="font-bold text-slate-900 leading-tight" x-text="i.name"></p>
                <div class="flex justify-between text-[11px] text-slate-600 mt-0.5">
                    <span x-text="i.qty + ' x ' + rupiah(i.price)"></span>
                    <span class="font-bold text-slate-900" x-text="rupiah(i.total)"></span>
                </div>
            </div>
        </template>
    </div>

    <div class="my-3 border-t border-dashed border-slate-300"></div>

    <div class="space-y-1 text-xs">
        <div class="flex justify-between text-slate-600">
            <span>Subtotal</span>
            <span class="font-semibold" x-text="rupiah(receipt.subtotal)"></span>
        </div>
        <div class="flex justify-between text-rose-600" x-show="receipt.discount > 0">
            <span>Diskon</span>
            <span class="font-semibold" x-text="'- ' + rupiah(receipt.discount)"></span>
        </div>
        <div class="flex justify-between text-sm font-black text-slate-950 pt-1 border-t border-dashed border-slate-200">
            <span>TOTAL AKHIR</span>
            <span x-text="rupiah(receipt.total)"></span>
        </div>
        <div class="flex justify-between text-slate-700 pt-0.5">
            <span>Diterima</span>
            <span class="font-semibold" x-text="rupiah(receipt.paid)"></span>
        </div>
        <div class="flex justify-between text-emerald-800 font-bold">
            <span>Kembalian</span>
            <span x-text="rupiah(receipt.change)"></span>
        </div>
    </div>

    <div class="my-3 border-t border-dashed border-slate-300"></div>

    <div class="text-center text-[10.5px] text-slate-500 space-y-0.5">
        <p class="font-semibold">Terima kasih atas kunjungan Anda!</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>
</div>

@if (session('status'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         class="mb-5 flex items-start justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <span>{{ session('status') }}</span>
        <button @click="show = false" class="font-bold text-emerald-600" aria-label="Tutup">&times;</button>
    </div>
@endif
@if (session('error'))
    <div x-data="{ show: true }" x-show="show"
         class="mb-5 flex items-start justify-between gap-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3.5 text-sm font-semibold text-amber-950 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="text-amber-700 text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="font-bold text-amber-700 text-base" aria-label="Tutup">&times;</button>
    </div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <p class="mb-1 font-semibold">Periksa kembali isian Anda:</p>
        <ul class="list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

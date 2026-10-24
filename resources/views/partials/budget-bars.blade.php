@foreach ($budgets as $b)
    @continue($b['budget'] === 0 && $b['spent'] === 0)
    @php
        $pct = $b['percent'];
        $bar = match ($b['status']) {
            'over' => 'bg-rose-600',
            'warn' => 'bg-amber-500',
            'ok' => 'bg-slate-900',
            default => 'bg-slate-300'
        };
        $statusBadge = match ($b['status']) {
            'over' => 'bg-rose-50 text-rose-700 border border-rose-200/60',
            'warn' => 'bg-amber-50 text-amber-700 border border-amber-200/60',
            'ok' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-500'
        };
    @endphp
    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3.5">
        <div class="mb-1.5 flex items-baseline justify-between gap-2">
            <span class="text-xs font-bold text-slate-900">{{ $b['category'] }}</span>
            <span class="font-mono text-xs text-slate-600">
                <strong class="font-bold text-slate-900">@rp($b['spent'])</strong>
                @if ($b['budget'] > 0)
                    <span class="text-slate-400">/ @rp($b['budget'])</span>
                @else
                    <span class="text-[10px] text-slate-400 italic">(tanpa pagu)</span>
                @endif
            </span>
        </div>

        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200/70">
            <div class="h-full rounded-full transition-all duration-300 {{ $bar }}"
                 style="width: {{ $pct === null ? 100 : min(100, $pct) }}%"></div>
        </div>

        <div class="mt-2 flex items-center justify-between text-[11px]">
            @if ($b['budget'] > 0)
                <span class="{{ $b['remaining'] < 0 ? 'font-bold text-rose-600' : 'text-slate-500' }}">
                    @if ($b['remaining'] < 0)
                        Melebihi anggaran <span class="font-mono font-bold">@rp(abs($b['remaining']))</span>
                    @else
                        Sisa anggaran <span class="font-mono font-semibold text-slate-800">@rp($b['remaining'])</span>
                    @endif
                </span>
                <span class="badge {{ $statusBadge }} font-mono text-[10px]">
                    {{ $pct }}%
                </span>
            @else
                <span class="text-slate-400 text-[10px]">Belum ditentukan pagu anggaran</span>
                <span class="badge bg-slate-100 text-slate-500 font-mono text-[10px]">&infin;</span>
            @endif
        </div>
    </div>
@endforeach

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseDrawerRequest;
use App\Http\Requests\OpenDrawerRequest;
use App\Models\CashDrawer;
use App\Services\CashDrawerService;
use Illuminate\Http\RedirectResponse;

class CashDrawerController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $drawers = CashDrawer::query()
            ->with('user:id,name')
            ->when(! $user->isOwner(), fn ($q) => $q->where('user_id', $user->id))
            ->latest('opened_at')
            ->paginate(15);

        $current = $user->openDrawer();

        return view('drawers.index', [
            'drawers' => $drawers,
            'current' => $current,
            'currentExpected' => $current?->computeExpectedCash(),
        ]);
    }

    public function open(OpenDrawerRequest $request, CashDrawerService $service): RedirectResponse
    {
        $service->open($request->user(), (int) $request->validated('opening_cash'));

        return redirect()->route('pos')->with('status', 'Shift dibuka. Selamat bertugas!');
    }

    public function close(CloseDrawerRequest $request, CashDrawer $drawer, CashDrawerService $service): RedirectResponse
    {
        abort_unless($drawer->user_id === $request->user()->id || $request->user()->isOwner(), 403);
        abort_unless($drawer->isOpen(), 422, 'Shift sudah ditutup.');

        $closed = $service->close($drawer, (int) $request->validated('closing_cash'), $request->validated('note'));

        $diff = $closed->difference;
        $msg = $diff === 0 ? 'Shift ditutup, kas pas.' : 'Shift ditutup. Selisih kas: Rp '.number_format($diff, 0, ',', '.');

        return redirect()->route('drawers.index')->with('status', $msg);
    }
}

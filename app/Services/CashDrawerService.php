<?php

namespace App\Services;

use App\Models\CashDrawer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashDrawerService
{
    public function open(User $user, int $openingCash): CashDrawer
    {
        if ($user->openDrawer()) {
            throw ValidationException::withMessages(['opening_cash' => 'Anda masih memiliki shift yang terbuka.']);
        }

        return CashDrawer::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => $openingCash,
            'status' => 'open',
        ]);
    }

    public function close(CashDrawer $drawer, int $closingCash, ?string $note): CashDrawer
    {
        return DB::transaction(function () use ($drawer, $closingCash, $note) {
            $expected = $drawer->computeExpectedCash();

            $drawer->update([
                'closed_at' => now(),
                'expected_cash' => $expected,
                'closing_cash' => $closingCash,
                'difference' => $closingCash - $expected,
                'status' => 'closed',
                'note' => $note,
            ]);

            return $drawer;
        });
    }
}

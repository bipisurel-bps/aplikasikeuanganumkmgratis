<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashDrawer extends Model
{
    protected $fillable = [
        'user_id', 'opened_at', 'closed_at', 'opening_cash', 'expected_cash',
        'closing_cash', 'difference', 'status', 'note',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'opening_cash' => 'integer',
            'expected_cash' => 'integer',
            'closing_cash' => 'integer',
            'difference' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /** Uang tunai yang seharusnya ada di laci: modal awal + penjualan tunai shift ini. */
    public function computeExpectedCash(): int
    {
        return $this->opening_cash + (int) $this->sales()->where('payment_method', 'cash')->sum('total');
    }
}

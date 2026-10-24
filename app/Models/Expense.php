<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Expense extends Model
{
    protected $fillable = ['user_id', 'category', 'description', 'amount', 'spent_on', 'receipt_path'];

    protected function casts(): array
    {
        return ['spent_on' => 'date', 'amount' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashMutations(): MorphMany
    {
        return $this->morphMany(CashMutation::class, 'source');
    }
}

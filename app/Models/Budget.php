<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['category', 'period', 'amount'];

    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }
}

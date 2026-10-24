<?php

namespace App\Events;

use App\Models\Purchase;
use Illuminate\Foundation\Events\Dispatchable;

class PurchaseReceived
{
    use Dispatchable;

    public function __construct(public Purchase $purchase)
    {
    }
}

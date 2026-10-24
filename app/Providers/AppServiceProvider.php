<?php

namespace App\Providers;

use App\Events\ExpenseRecorded;
use App\Events\PurchaseReceived;
use App\Events\SaleCompleted;
use App\Listeners\PostCashJournal;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'sale' => Sale::class,
            'purchase' => Purchase::class,
            'expense' => Expense::class,
        ]);

        Event::listen(SaleCompleted::class, [PostCashJournal::class, 'onSale']);
        Event::listen(PurchaseReceived::class, [PostCashJournal::class, 'onPurchase']);
        Event::listen(ExpenseRecorded::class, [PostCashJournal::class, 'onExpense']);

        Blade::directive('rp', fn ($expression) => "<?php echo 'Rp '.number_format((int) ({$expression}), 0, ',', '.'); ?>");

        Gate::define('owner', fn ($user) => $user->isOwner());
    }
}

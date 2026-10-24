<?php

namespace Tests\Feature;

use App\Models\CashMutation;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CashDrawerService;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PosFlowTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['role' => 'owner']);
    }

    private function kasir(): User
    {
        return User::factory()->create(['role' => 'kasir']);
    }

    private function product(array $o = []): Product
    {
        return Product::create($o + [
            'sku' => 'SKU-'.uniqid(), 'name' => 'Kopi', 'stock' => 10, 'min_stock' => 2,
            'cost_price' => 1000, 'sell_price' => 2000,
        ]);
    }

    public function test_moving_average_cost(): void
    {
        $this->assertSame(1500, PurchaseService::movingAverage(10, 1000, 10, 2000));
        $this->assertSame(2000, PurchaseService::movingAverage(0, 0, 5, 2000));
    }

    public function test_checkout_cuts_stock_records_cogs_and_cash(): void
    {
        $kasir = $this->kasir();
        $p = $this->product();
        app(CashDrawerService::class)->open($kasir, 100000);

        $res = $this->actingAs($kasir)->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $p->id, 'qty' => 3]],
            'discount' => 500, 'paid' => 10000, 'payment_method' => 'cash',
        ])->assertCreated();

        $res->assertJsonPath('sale.total', 5500)->assertJsonPath('sale.change', 4500);
        $this->assertSame(7, $p->fresh()->stock);
        $this->assertDatabaseHas('sales', ['total' => 5500, 'cogs' => 3000]);
        $this->assertDatabaseHas('cash_mutations', ['direction' => 'in', 'amount' => 5500]);
    }

    public function test_checkout_rejects_insufficient_stock_and_payment(): void
    {
        $kasir = $this->kasir();
        $p = $this->product(['stock' => 2]);
        app(CashDrawerService::class)->open($kasir, 0);

        $this->actingAs($kasir)->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $p->id, 'qty' => 5]], 'paid' => 99999, 'payment_method' => 'cash',
        ])->assertUnprocessable();

        $this->actingAs($kasir)->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $p->id, 'qty' => 2]], 'paid' => 100, 'payment_method' => 'cash',
        ])->assertUnprocessable();

        $this->assertSame(2, $p->fresh()->stock);
        $this->assertSame(0, CashMutation::count());
    }

    public function test_checkout_requires_open_shift(): void
    {
        $p = $this->product();
        $this->actingAs($this->kasir())->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $p->id, 'qty' => 1]], 'paid' => 5000, 'payment_method' => 'cash',
        ])->assertUnprocessable()->assertJsonValidationErrors('shift');
    }

    public function test_restock_updates_stock_average_cost_and_cash_out(): void
    {
        $owner = $this->owner();
        $p = $this->product(['stock' => 10, 'cost_price' => 1000]);
        $s = Supplier::create(['name' => 'UD Test']);

        $this->actingAs($owner)->post(route('purchases.store'), [
            'supplier_id' => $s->id, 'purchased_on' => now()->toDateString(), 'payment_method' => 'cash',
            'items' => [['product_id' => $p->id, 'qty' => 10, 'unit_cost' => 2000]],
        ])->assertRedirect(route('purchases.index'));

        $p->refresh();
        $this->assertSame(20, $p->stock);
        $this->assertSame(1500, $p->cost_price);
        $this->assertDatabaseHas('cash_mutations', ['direction' => 'out', 'amount' => 20000]);
    }

    public function test_expense_with_receipt_posts_to_cash_and_delete_reverses(): void
    {
        Storage::fake('local');
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('expenses.store'), [
            'category' => 'Listrik & Air', 'description' => 'Token', 'amount' => 50000,
            'spent_on' => now()->toDateString(),
            'receipt' => UploadedFile::fake()->create('nota.pdf', 50, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $expense = \App\Models\Expense::first();
        Storage::disk('local')->assertExists($expense->receipt_path);
        $this->assertDatabaseHas('cash_mutations', ['direction' => 'out', 'amount' => 50000]);

        $this->actingAs($owner)->delete(route('expenses.destroy', $expense));
        $this->assertSame(0, CashMutation::count());
        Storage::disk('local')->assertMissing($expense->receipt_path);
    }

    public function test_kasir_cannot_access_owner_pages(): void
    {
        $kasir = $this->kasir();
        foreach (['dashboard', 'products.index', 'suppliers.index', 'purchases.index', 'expenses.index', 'cash.index'] as $r) {
            $this->actingAs($kasir)->get(route($r))->assertForbidden();
        }
        $this->actingAs($kasir)->get(route('pos'))->assertOk();
    }

    public function test_landing_page_renders_successfully(): void
    {
        $this->get(route('landing'))->assertOk()->assertSee('Bukan Sekadar Kasir POS Biasa');
    }

    public function test_all_pages_render_for_owner_with_seeded_data(): void
    {
        $this->seed();
        $owner = User::where('role', 'owner')->first();

        foreach ([
            route('dashboard'), route('pos'), route('sales.index'), route('drawers.index'),
            route('products.index'), route('suppliers.index'), route('purchases.index'),
            route('purchases.create'), route('expenses.index'), route('cash.index'),
            route('sales.show', \App\Models\Sale::first()),
            route('purchases.show', \App\Models\Purchase::first()),
        ] as $url) {
            $this->actingAs($owner)->get($url)->assertOk();
        }
    }

    public function test_drawer_close_computes_difference(): void
    {
        $kasir = $this->kasir();
        $p = $this->product();
        $svc = app(CashDrawerService::class);
        $drawer = $svc->open($kasir, 100000);

        $this->actingAs($kasir)->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $p->id, 'qty' => 1]], 'paid' => 2000, 'payment_method' => 'cash',
        ])->assertCreated();

        $closed = $svc->close($drawer, 101000, null);
        $this->assertSame(102000, $closed->expected_cash);
        $this->assertSame(-1000, $closed->difference);
    }
}

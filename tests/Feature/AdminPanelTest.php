<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_successfully(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@toramally.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals(1, session('admin_user_id'));
    }

    public function test_admin_dashboard_renders(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Atelier Overview');
        $response->assertSee('Total Revenue');
    }

    public function test_admin_orders_index_renders(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin/orders');
        $response->assertStatus(200);
        $response->assertSee('Orders & Workshop Requests');
    }

    public function test_admin_inventory_index_renders_and_updates(): void
    {
        $product = Product::first();
        if ($product) {
            $response = $this->withSession(['admin_user_id' => 1])->get('/admin/inventory');
            $response->assertStatus(200);
            $response->assertSee('Inventory & Workshop Stock');

            $updateResponse = $this->withSession(['admin_user_id' => 1])
                ->postJson('/admin/inventory/update', [
                    'product_id' => $product->id,
                    'size' => 'UK 8',
                    'qty' => 5,
                ]);

            $updateResponse->assertStatus(200);
            $updateResponse->assertJson(['ok' => true, 'qty' => 5]);

            $this->assertDatabaseHas('product_stock', [
                'product_id' => $product->id,
                'size' => 'UK 8',
                'qty' => 5,
            ]);
        }
    }

    public function test_admin_products_index_renders(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Catalogue & Silhouettes');
    }

    public function test_admin_commissions_index_renders(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin/commissions');
        $response->assertStatus(200);
        $response->assertSee('Bespoke Builder Commissions');
    }

    public function test_admin_settings_renders_and_updates(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('Atelier & Store Configuration');

        $updateResponse = $this->withSession(['admin_user_id' => 1])
            ->post('/admin/settings', [
                'whatsapp' => '+919999999999',
            ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'k' => 'whatsapp',
            'v' => '+919999999999',
        ]);
    }
}

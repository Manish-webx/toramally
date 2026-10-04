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

    public function test_admin_can_view_categories_index(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('Catalogue Categories');
        $response->assertSee('Men');
        $response->assertSee('+ Add New Category');
    }

    public function test_admin_can_view_category_create_form(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->get('/admin/categories/create');
        $response->assertStatus(200);
        $response->assertSee('New Atelier Category');
        $response->assertSee('Category Name');
    }

    public function test_admin_can_store_new_category(): void
    {
        $response = $this->withSession(['admin_user_id' => 1])->post('/admin/categories', [
            'name' => 'Boots',
            'slug' => 'boots',
            'description' => 'Fine Goodyear welted Chelsea and Jodhpur boots.',
            'sort' => 25,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Boots',
            'slug' => 'boots',
        ]);
    }

    public function test_admin_can_edit_and_update_category(): void
    {
        $category = \App\Models\Category::first();

        $editResponse = $this->withSession(['admin_user_id' => 1])->get("/admin/categories/{$category->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee("Edit Category: {$category->name}");

        $updateResponse = $this->withSession(['admin_user_id' => 1])->post("/admin/categories/{$category->id}", [
            'name' => $category->name . ' Updated',
            'slug' => $category->slug,
            'description' => 'Updated category description',
            'sort' => 99,
            'active' => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name . ' Updated',
            'sort' => 99,
        ]);
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $category = \App\Models\Category::first();
        $initialStatus = $category->active;

        $response = $this->withSession(['admin_user_id' => 1])->post("/admin/categories/{$category->id}/toggle");
        $response->assertRedirect();

        $category->refresh();
        $this->assertEquals(!$initialStatus, $category->active);
    }

    public function test_admin_can_delete_unused_category(): void
    {
        $category = \App\Models\Category::create([
            'name' => 'Temporary Cat',
            'slug' => 'temp-cat',
            'active' => 1,
        ]);

        $response = $this->withSession(['admin_user_id' => 1])->post("/admin/categories/{$category->id}/delete");
        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $category = \App\Models\Category::where('name', 'Men')->first();

        $response = $this->withSession(['admin_user_id' => 1])->post("/admin/categories/{$category->id}/delete");
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Men',
        ]);
    }

    public function test_deactivated_category_and_its_products_are_hidden_from_storefront(): void
    {
        $category = \App\Models\Category::where('name', 'Men')->first();

        // Initially Men category and Taus product render
        $this->get('/shop/men')->assertStatus(200);
        $this->get('/shop/men/belgian-loafers/taus')->assertStatus(200);

        // Deactivate Men category
        $category->update(['active' => 0]);

        // Shop /shop/men and product page should now 404
        $this->get('/shop/men')->assertStatus(404);
        $this->get('/shop/men/belgian-loafers/taus')->assertStatus(404);

        // published_products helper excludes Men products
        $allProducts = published_products();
        $this->assertEmpty(array_filter($allProducts, fn($p) => $p['category'] === 'Men'));

        // Re-activate Men category
        $category->update(['active' => 1]);
        $this->get('/shop/men')->assertStatus(200);
        $this->get('/shop/men/belgian-loafers/taus')->assertStatus(200);
    }

    public function test_admin_can_create_and_update_product_with_variant_images(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $fakeImage1 = \Illuminate\Http\UploadedFile::fake()->image('cognac_side.jpg');
        $fakeImage2 = \Illuminate\Http\UploadedFile::fake()->image('black_angle.jpg');

        $response = $this->withSession(['admin_user_id' => 1])->post('/admin/products', [
            'name' => 'Royal Loafer',
            'slug' => 'royal-loafer',
            'category' => 'Men',
            'silhouette' => 'Loafer',
            'base_price' => 25000,
            'availability' => 'Made to Order',
            'lead_min' => 4,
            'lead_max' => 6,
            'status' => 'Published',
            'colour_names' => ['Cognac', 'Black'],
            'colour_hexes' => ['#8f4b21', '#000000'],
            'new_images' => [$fakeImage1, $fakeImage2],
            'new_image_colours' => ['Cognac', 'Black'],
            'new_image_kinds' => ['side', 'angle'],
            'new_image_alts' => ['Royal Loafer Cognac', 'Royal Loafer Black Angle'],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'royal-loafer')->firstOrFail();
        $this->assertCount(2, $product->colours);
        $this->assertCount(2, $product->images);

        $cognacCol = $product->colours()->where('name', 'Cognac')->first();
        $blackCol = $product->colours()->where('name', 'Black')->first();

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'colour_id' => $cognacCol->id,
            'kind' => 'side',
        ]);

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'colour_id' => $blackCol->id,
            'kind' => 'angle',
        ]);

        // Test storefront renders product page with images
        $pdpResponse = $this->get('/shop/men/loafers/royal-loafer');
        $pdpResponse->assertStatus(200);
        $pdpResponse->assertSee('Royal Loafer');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomerAuthAndOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_and_register_pages_render(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('Sign In');
        $this->get('/register')->assertStatus(200)->assertSee('Create Customer Account');
    }

    public function test_customer_can_register_with_all_required_information(): void
    {
        $payload = [
            'first_name' => 'Vikram',
            'last_name'  => 'Roy',
            'email'      => 'vikram.roy@example.com',
            'phone'      => '+91 98300 12345',
            'password'   => 'secret123',
            'line1'      => '12 Camac Street, Apartment 7A',
            'line2'      => 'Near Elgin Road',
            'city'       => 'Kolkata',
            'state'      => 'West Bengal',
            'postcode'   => '700017',
            'country'    => 'India',
            'marketing_opt_in' => '1',
        ];

        $response = $this->post('/register', $payload);
        $response->assertRedirect('/account');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('customers', [
            'email'      => 'vikram.roy@example.com',
            'first_name' => 'Vikram',
            'last_name'  => 'Roy',
            'phone'      => '+91 98300 12345',
        ]);

        $this->assertDatabaseHas('customer_addresses', [
            'line1'    => '12 Camac Street, Apartment 7A',
            'city'     => 'Kolkata',
            'state'    => 'West Bengal',
            'postcode' => '700017',
        ]);
    }

    public function test_customer_can_login_and_access_account_page(): void
    {
        $customer = Customer::create([
            'first_name' => 'Ananya',
            'last_name'  => 'Sen',
            'email'      => 'ananya@example.com',
            'phone'      => '+91 98111 22233',
            'password'   => 'password123',
            'status'     => 'active',
        ]);

        $response = $this->post('/login', [
            'email'    => 'ananya@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();

        $accountPage = $this->get('/account');
        $accountPage->assertStatus(200);
        $accountPage->assertSee('Welcome, Ananya');
    }

    public function test_order_creation_requires_account_creation_if_guest(): void
    {
        $product = Product::first();
        $price = $product ? $product->price : 28000;
        $name = $product ? $product->name : 'Taus';
        $slug = $product ? $product->slug : 'taus';

        // Attempting without required account details should fail with 422
        $response = $this->postJson('/api/order', [
            'currency' => 'INR',
            'bag'      => [
                [
                    'key'    => $slug,
                    'name'   => $name,
                    'colour' => 'Cognac',
                    'size'   => 'UK 8',
                    'price'  => $price,
                    'qty'    => 1,
                ]
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('requires_account', true);

        // Providing all customer information (name, email, phone, password, location, pincode) creates account and order
        $orderPayload = [
            'currency'   => 'INR',
            'first_name' => 'Aditya',
            'last_name'  => 'Verma',
            'email'      => 'aditya.verma@example.com',
            'phone'      => '+91 98765 43210',
            'password'   => 'toramally2026',
            'line1'      => 'Flat 302, Green Glen Layout, Bellandur',
            'city'       => 'Bengaluru',
            'state'      => 'Karnataka',
            'postcode'   => '560103',
            'country'    => 'India',
            'gift'       => 'Please pack in luxury gift box.',
            'bag'        => [
                [
                    'key'    => $slug,
                    'name'   => $name,
                    'colour' => 'Cognac',
                    'size'   => 'UK 8',
                    'price'  => $price,
                    'qty'    => 1,
                ]
            ],
        ];

        $orderResponse = $this->postJson('/api/order', $orderPayload);
        $orderResponse->assertStatus(200);
        $orderResponse->assertJsonPath('ok', true);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('customers', [
            'email'      => 'aditya.verma@example.com',
            'first_name' => 'Aditya',
        ]);

        $this->assertDatabaseHas('customer_addresses', [
            'line1'    => 'Flat 302, Green Glen Layout, Bellandur',
            'city'     => 'Bengaluru',
            'postcode' => '560103',
        ]);

        $this->assertDatabaseHas('orders', [
            'email'         => 'aditya.verma@example.com',
            'ship_line1'    => 'Flat 302, Green Glen Layout, Bellandur',
            'ship_city'     => 'Bengaluru',
            'ship_postcode' => '560103',
            'subtotal_inr'  => $price,
        ]);

        $this->assertDatabaseHas('order_items', [
            'name'           => $name,
            'unit_price_inr' => $price,
        ]);
    }

    public function test_customer_can_update_personal_profile(): void
    {
        $customer = Customer::create([
            'first_name' => 'Nitesh',
            'last_name'  => 'Kushwaha',
            'email'      => 'nitesh@example.com',
            'phone'      => '9876543210',
            'password'   => 'initialpass',
            'status'     => 'active',
        ]);

        $this->actingAs($customer);

        $response = $this->post('/account/profile', [
            'first_name' => 'Nitesh Kumar',
            'last_name'  => 'Kushwaha',
            'phone'      => '9811223344',
        ]);

        $response->assertRedirect('/account');
        $this->assertDatabaseHas('customers', [
            'id'         => $customer->id,
            'first_name' => 'Nitesh Kumar',
            'phone'      => '9811223344',
        ]);
    }

    public function test_customer_can_update_delivery_address(): void
    {
        $customer = Customer::create([
            'first_name' => 'Nitesh',
            'last_name'  => 'Kushwaha',
            'email'      => 'nitesh2@example.com',
            'phone'      => '9876543210',
            'password'   => 'initialpass',
            'status'     => 'active',
        ]);

        $this->actingAs($customer);

        $response = $this->post('/account/address', [
            'name'     => 'Nitesh Kushwaha',
            'line1'    => 'Near R K Tent House Khansa Road',
            'line2'    => 'Sector 10',
            'city'     => 'Gurugram',
            'state'    => 'Haryana',
            'postcode' => '122001',
            'country'  => 'India',
            'phone'    => '9876543210',
        ]);

        $response->assertRedirect('/account');
        $this->assertDatabaseHas('customer_addresses', [
            'customer_id' => $customer->id,
            'line1'       => 'Near R K Tent House Khansa Road',
            'city'        => 'Gurugram',
            'state'       => 'Haryana',
            'postcode'    => '122001',
        ]);
    }
}

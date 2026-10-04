<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToramallyPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Tōramally');
    }

    public function test_shop_page_renders_successfully(): void
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('All pairs');
    }

    public function test_shop_category_renders_successfully(): void
    {
        $response = $this->get('/shop/men');
        $response->assertStatus(200);
        $response->assertSee('Men');
    }

    public function test_product_detail_renders_successfully(): void
    {
        $response = $this->get('/shop/men/belgian-loafers/taus');
        $response->assertStatus(200);
        $response->assertSee('Taus');
    }

    public function test_craft_pages_render_successfully(): void
    {
        $response = $this->get('/craft');
        $response->assertStatus(200);
        $response->assertSee('The craft ladder');

        $response = $this->get('/craft/patina');
        $response->assertStatus(200);
        $response->assertSee('Patina');
    }

    public function test_bespoke_and_builder_render_successfully(): void
    {
        $response = $this->get('/bespoke');
        $response->assertStatus(200);

        $response = $this->get('/bespoke/build');
        $response->assertStatus(200);

        $response = $this->get('/bespoke/wedding');
        $response->assertStatus(200);
    }

    public function test_house_and_journal_render_successfully(): void
    {
        $response = $this->get('/house');
        $response->assertStatus(200);

        $response = $this->get('/journal');
        $response->assertStatus(200);

        $response = $this->get('/journal/what-is-scarring');
        $response->assertStatus(200);
        $response->assertSee('What is scarring?');
    }

    public function test_policies_and_simple_pages_render_successfully(): void
    {
        $routes = ['care', 'restoration', 'size-guide', 'faq', 'visit', 'contact', 'privacy', 'terms', 'shipping', 'returns', 'cookies'];
        foreach ($routes as $r) {
            $response = $this->get('/' . $r);
            $response->assertStatus(200);
        }
    }

    public function test_api_search_returns_json(): void
    {
        $response = $this->getJson('/api/search?q=taus');
        $response->assertStatus(200);
        $response->assertJsonPath('ok', true);
    }

    public function test_api_newsletter_subscription(): void
    {
        $response = $this->postJson('/api/newsletter', [
            'email' => 'client@example.com'
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('ok', true);
        $this->assertDatabaseHas('subscribers', [
            'email' => 'client@example.com'
        ]);
    }
}

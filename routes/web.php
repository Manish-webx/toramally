<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Panel Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'loginSubmit'])->name('admin.login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        // Orders Management
        Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('admin.orders.index');
        Route::get('/orders/{id}', [AdminController::class, 'orderShow'])->name('admin.orders.show');
        Route::post('/orders/{id}/status', [AdminController::class, 'orderUpdateStatus'])->name('admin.orders.status');
        Route::post('/orders/{id}/payment', [AdminController::class, 'orderUpdatePayment'])->name('admin.orders.payment');
        Route::post('/orders/{id}/notes', [AdminController::class, 'orderSaveNotes'])->name('admin.orders.notes');
        Route::get('/orders/{id}/invoice', [AdminController::class, 'orderInvoice'])->name('admin.orders.invoice');

        // Inventory Management
        Route::get('/inventory', [AdminController::class, 'inventoryIndex'])->name('admin.inventory.index');
        Route::post('/inventory/update', [AdminController::class, 'inventoryUpdate'])->name('admin.inventory.update');

        // Products & Silhouettes
        Route::get('/products', [AdminController::class, 'productsIndex'])->name('admin.products.index');
        Route::get('/products/create', [AdminController::class, 'productCreate'])->name('admin.products.create');
        Route::post('/products', [AdminController::class, 'productStore'])->name('admin.products.store');
        Route::get('/products/{id}/edit', [AdminController::class, 'productEdit'])->name('admin.products.edit');
        Route::post('/products/{id}', [AdminController::class, 'productUpdate'])->name('admin.products.update');
        Route::post('/products/{id}/toggle', [AdminController::class, 'productToggleStatus'])->name('admin.products.toggle');

        // Bespoke Commissions
        Route::get('/commissions', [AdminController::class, 'commissionsIndex'])->name('admin.commissions.index');
        Route::get('/commissions/{id}', [AdminController::class, 'commissionShow'])->name('admin.commissions.show');
        Route::post('/commissions/{id}', [AdminController::class, 'commissionUpdate'])->name('admin.commissions.update');

        // Customers & Patrons
        Route::get('/customers', [AdminController::class, 'customersIndex'])->name('admin.customers.index');
        Route::get('/customers/{id}', [AdminController::class, 'customerShow'])->name('admin.customers.show');

        // Enquiries & Appointments
        Route::get('/enquiries', [AdminController::class, 'enquiriesIndex'])->name('admin.enquiries.index');
        Route::post('/enquiries/{id}/status', [AdminController::class, 'enquiryUpdateStatus'])->name('admin.enquiries.status');

        // Subscribers
        Route::get('/subscribers', [AdminController::class, 'subscribersIndex'])->name('admin.subscribers.index');

        // Settings
        Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('admin.settings.index');
        Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('admin.settings.update');
    });
});

// Home
Route::get('/', [PageController::class, 'home'])->name('home');

// Shop & Products
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/shop/{catKey}', [PageController::class, 'shop'])->name('shop.cat');
Route::get('/shop/{catKey}/{sub}', [PageController::class, 'shop'])->name('shop.sub');
Route::get('/shop/{catKey}/{sub}/{productSlug}', [PageController::class, 'shop'])->name('shop.product');

Route::get('/collection/{slug}', [PageController::class, 'collection'])->name('collection');

// Craft Ladder
Route::get('/craft', [PageController::class, 'craftIndex'])->name('craft.index');
Route::get('/craft/{slug}', [PageController::class, 'craft'])->name('craft.show');

// Bespoke & Builder
Route::get('/bespoke', [PageController::class, 'bespoke'])->name('bespoke');
Route::get('/bespoke/{sub}', [PageController::class, 'bespoke'])->name('bespoke.sub');

// House & Heritage
Route::get('/house', [PageController::class, 'house'])->name('house');
Route::get('/house/{sub}', [PageController::class, 'house'])->name('house.sub');

// Journal
Route::get('/journal', [PageController::class, 'journal'])->name('journal');
Route::get('/journal/{slug}', [PageController::class, 'article'])->name('journal.article');

// Customer Authentication & Account Management
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/account', [AuthController::class, 'account'])->name('account');
Route::post('/account/profile', [AuthController::class, 'updateProfile'])->name('account.profile');
Route::post('/account/address', [AuthController::class, 'updateAddress'])->name('account.address');

// Simple info & service pages
Route::get('/care', fn() => app(PageController::class)->simple('care'))->name('care');
Route::get('/restoration', fn() => app(PageController::class)->simple('restoration'))->name('restoration');
Route::get('/size-guide', fn() => app(PageController::class)->simple('size-guide'))->name('size-guide');
Route::get('/faq', fn() => app(PageController::class)->simple('faq'))->name('faq');
Route::get('/visit', fn() => app(PageController::class)->simple('visit'))->name('visit');
Route::get('/appointments', fn() => app(PageController::class)->simple('visit'))->name('appointments');
Route::get('/contact', fn() => app(PageController::class)->simple('contact'))->name('contact');
Route::get('/received', fn() => app(PageController::class)->simple('received'))->name('received');

// Policies
Route::get('/shipping', fn() => app(PageController::class)->policy('shipping'))->name('policy.shipping');
Route::get('/returns', fn() => app(PageController::class)->policy('returns'))->name('policy.returns');
Route::get('/privacy', fn() => app(PageController::class)->policy('privacy'))->name('policy.privacy');
Route::get('/terms', fn() => app(PageController::class)->policy('terms'))->name('policy.terms');
Route::get('/cookies', fn() => app(PageController::class)->policy('cookies'))->name('policy.cookies');

// Search
Route::get('/search', [PageController::class, 'search'])->name('search');

// Legacy product redirects (/products/{slug})
Route::get('/products/{slug}', function ($slug) {
    $p = product_by_slug($slug);
    if ($p) {
        return redirect(product_url($p), 301);
    }
    abort(404);
});

// APIs (compatible with JS fetch calls)
Route::prefix('api')->group(function () {
    Route::get('/search', [ApiController::class, 'search'])->name('api.search');
    Route::post('/form', [ApiController::class, 'form'])->name('api.form');
    Route::post('/order', [ApiController::class, 'createOrder'])->name('api.order');
    Route::post('/commission', [ApiController::class, 'commission'])->name('api.commission');
    Route::post('/newsletter', [ApiController::class, 'newsletter'])->name('api.newsletter');
    Route::get('/auth/status', [AuthController::class, 'status'])->name('api.auth.status');
});

// Direct top-level single slug handler (e.g., /shoe-shine-service)
Route::get('/{slug}', [PageController::class, 'directProduct'])->where('slug', '[a-zA-Z0-9\-_]+')->name('direct.product');


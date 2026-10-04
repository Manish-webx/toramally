<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 190)->unique();
            $table->string('password_hash', 255)->nullable();
            $table->string('first_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('country', 60)->nullable();
            $table->boolean('is_patron')->default(false);
            $table->date('patron_since')->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('active');
            $table->dateTime('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('label', 40)->nullable();
            $table->string('name', 120);
            $table->string('line1', 190);
            $table->string('line2', 190)->nullable();
            $table->string('city', 80);
            $table->string('state', 80)->nullable();
            $table->string('postcode', 20);
            $table->string('country', 60)->default('India');
            $table->string('phone', 40)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::create('customer_sizes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('label', 60)->nullable();
            $table->string('size_uk', 10);
            $table->string('last_name', 40)->nullable();
            $table->decimal('foot_cm', 4, 1)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('product_id');
            $table->primary(['customer_id', 'product_id']);
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('owned_pairs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('pair_name', 160);
            $table->string('purchased_from', 60)->default('Kolkata flagship');
            $table->unsignedSmallInteger('purchase_year')->nullable();
            $table->string('size_uk', 10)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->nullable()->unique();
            $table->char('session_key', 64)->nullable()->index();
            $table->mediumText('data_json');
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', 20)->unique();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('email', 190);
            $table->string('phone', 40)->nullable();
            $table->string('ship_name', 120);
            $table->string('ship_line1', 190);
            $table->string('ship_line2', 190)->nullable();
            $table->string('ship_city', 80);
            $table->string('ship_state', 80)->nullable();
            $table->string('ship_postcode', 20);
            $table->string('ship_country', 60);
            $table->boolean('bill_same')->default(true);
            $table->text('bill_json')->nullable();
            $table->string('gstin_buyer', 20)->nullable();
            $table->char('currency', 3)->default('INR');
            $table->decimal('fx_rate', 10, 4)->default(1);
            $table->unsignedInteger('subtotal_inr');
            $table->unsignedInteger('patron_benefit_inr')->default(0);
            $table->unsignedInteger('code_benefit_inr')->default(0);
            $table->unsignedInteger('shipping_inr')->default(0);
            $table->unsignedInteger('total_inr');
            $table->unsignedInteger('total_charged');
            $table->string('gift_note', 300)->nullable();
            $table->string('status', 40)->default('Pending Payment');
            $table->string('payment_status', 40)->default('Unpaid');
            $table->string('gateway_code', 30)->nullable();
            $table->boolean('is_international')->default(false);
            $table->unsignedInteger('shipping_rate_id')->nullable();
            $table->text('admin_notes')->nullable();
            $table->char('access_token', 32);
            $table->timestamps();

            $table->index(['customer_id']);
            $table->index(['status', 'payment_status']);
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->string('name', 160);
            $table->string('colour', 60)->nullable();
            $table->string('size', 20)->nullable();
            $table->unsignedSmallInteger('qty')->default(1);
            $table->unsignedInteger('unit_price_inr');
            $table->string('hsn_code', 12)->nullable();
            $table->text('personalisation_json')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->boolean('made_to_order')->default(true);
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::create('order_status_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('status', 40);
            $table->string('note', 500)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->boolean('customer_notified')->default(false);
            $table->unsignedInteger('admin_id')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->unique();
            $table->string('invoice_no', 30)->unique();
            $table->dateTime('issued_at');
            $table->mediumText('data_json');
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30)->unique();
            $table->string('name', 80);
            $table->boolean('enabled')->default(false);
            $table->string('mode', 20)->default('test');
            $table->string('region', 30)->default('both');
            $table->text('credentials_enc')->nullable();
            $table->string('display_label', 120)->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('gateway_code', 30);
            $table->string('gateway_ref', 120)->nullable();
            $table->string('gateway_payment_id', 120)->nullable();
            $table->unsignedInteger('amount');
            $table->char('currency', 3);
            $table->string('status', 30)->default('created');
            $table->boolean('verified')->default(false);
            $table->mediumText('raw_json')->nullable();
            $table->timestamps();
            $table->index(['gateway_code', 'gateway_ref']);
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::create('shipping_partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80);
            $table->string('driver', 30)->default('manual');
            $table->string('scope', 30)->default('domestic');
            $table->string('account_no', 80)->nullable();
            $table->text('credentials_enc')->nullable();
            $table->string('tracking_url_format', 255)->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('scope', 30); // domestic, international
            $table->string('name', 80);
            $table->string('countries', 500)->nullable();
            $table->unsignedInteger('rate_inr')->default(0);
            $table->unsignedInteger('free_above_inr')->nullable();
            $table->unsignedInteger('partner_id')->nullable();
            $table->string('eta_text', 80)->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('partner_id')->nullable();
            $table->string('tracking_no', 80)->nullable();
            $table->string('last_status', 120)->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('last_sync_at')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_partners');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('owned_pairs');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('customer_sizes');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};

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
        Schema::create('commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ref', 20)->unique();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('name', 120);
            $table->string('contact', 190);
            $table->text('build_json');
            $table->unsignedInteger('estimate_inr')->nullable();
            $table->unsignedInteger('quote_inr')->nullable();
            $table->string('status', 60)->default('Enquiry received');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kind', 30);
            $table->string('name', 120)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('phone', 60)->nullable();
            $table->text('payload_json');
            $table->string('status', 30)->default('New');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['kind', 'status']);
        });

        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner_type', 40); // enquiry, commission, owned_pair, order_item, customer
            $table->unsignedInteger('owner_id');
            $table->string('path', 255);
            $table->string('original_name', 190)->nullable();
            $table->string('mime', 80)->nullable();
            $table->unsignedInteger('size_bytes')->nullable();
            $table->string('kind', 30)->nullable();
            $table->timestamps();
            $table->index(['owner_type', 'owner_id']);
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 190)->unique();
            $table->string('source', 40)->default('footer');
            $table->string('status', 30)->default('subscribed');
            $table->timestamps();
        });

        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('email', 190)->unique();
            $table->string('password_hash', 255);
            $table->string('role', 30)->default('sales');
            $table->boolean('active')->default(true);
            $table->dateTime('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('scope', 30);
            $table->string('identifier', 190);
            $table->string('ip', 45);
            $table->boolean('success')->default(false);
            $table->timestamps();
            $table->index(['scope', 'identifier', 'created_at']);
            $table->index(['ip', 'created_at']);
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_id')->nullable();
            $table->string('action', 60);
            $table->string('entity', 40)->nullable();
            $table->unsignedInteger('entity_id')->nullable();
            $table->string('details', 500)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('email_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('to_email', 190);
            $table->string('subject', 255);
            $table->string('template', 60)->nullable();
            $table->string('status', 30);
            $table->string('error', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_log');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('commissions');
    }
};

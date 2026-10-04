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
        Schema::create('settings', function (Blueprint $table) {
            $table->string('k', 80)->primary();
            $table->text('v')->nullable();
            $table->timestamps();
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 80)->unique();
            $table->string('type', 20)->default('collection'); // craft, collection
            $table->string('name', 120);
            $table->string('scale_word', 60)->nullable();
            $table->string('tagline', 160)->nullable();
            $table->text('description')->nullable();
            $table->text('how_1')->nullable();
            $table->text('how_2')->nullable();
            $table->text('how_3')->nullable();
            $table->unsignedInteger('from_price')->nullable();
            $table->string('lead_weeks', 30)->nullable();
            $table->string('buy_mode_note', 160)->nullable();
            $table->boolean('on_ladder')->default(false);
            $table->integer('sort')->default(0);
            $table->boolean('active')->default(true);
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_desc', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 120)->unique();
            $table->string('name', 160);
            $table->string('poetic', 255)->nullable();
            $table->text('story')->nullable();
            $table->string('category', 40); // Men, Women, Accessories, Everyday, Service
            $table->string('line', 40)->default(''); // Classic, Special Occasion
            $table->string('silhouette', 80);
            $table->string('last_name', 40)->nullable();
            $table->unsignedInteger('craft_id')->nullable();
            $table->string('personalisation_level', 40)->default('House Design');
            $table->string('construction', 60)->nullable();
            $table->string('material', 60)->default('Calf')->nullable();
            $table->unsignedInteger('base_price');
            $table->string('availability', 40)->default('Made to Order');
            $table->unsignedTinyInteger('lead_min')->default(5);
            $table->unsignedTinyInteger('lead_max')->default(7);
            $table->string('buy_mode', 40)->default('Add to Bag');
            $table->string('occasions', 120)->nullable();
            $table->string('size_type', 40)->default('shoe_men');
            $table->boolean('patron_eligible')->default(true);
            $table->boolean('featured')->default(false);
            $table->text('drawing_json')->nullable();
            $table->string('hsn_code', 12)->default('6403')->nullable();
            $table->unsignedInteger('weight_g')->nullable();
            $table->string('customs_desc', 160)->nullable();
            $table->string('status', 40)->default('Published');
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_desc', 255)->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->index(['category', 'line', 'status']);
            $table->foreign('craft_id')->references('id')->on('collections')->nullOnDelete();
        });

        Schema::create('product_collection', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('collection_id');
            $table->primary(['product_id', 'collection_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('collection_id')->references('id')->on('collections')->cascadeOnDelete();
        });

        Schema::create('product_colours', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('name', 60);
            $table->char('hex', 7)->default('#5e1f2c');
            $table->integer('price_diff')->default(0);
            $table->integer('sort')->default(0);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('colour_id')->nullable();
            $table->string('path', 255);
            $table->string('alt', 255)->nullable();
            $table->string('kind', 40)->default('angle');
            $table->integer('sort')->default(0);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('product_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('colour_id')->nullable();
            $table->string('size', 20);
            $table->integer('qty')->default(0);
            $table->unique(['product_id', 'colour_id', 'size']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('page', 40)->default('home');
            $table->string('block_key', 40);
            $table->text('data_json')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->unique(['page', 'block_key']);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 80)->unique();
            $table->string('title', 160);
            $table->mediumText('body_html')->nullable();
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_desc', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('press_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('note', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('kind', 40)->default('press');
            $table->integer('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('celebrities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('occasion', 160)->nullable();
            $table->string('pair_worn', 160)->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->boolean('consent_on_file')->default(false);
            $table->integer('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 160);
            $table->string('url', 255);
            $table->string('poster_path', 255)->nullable();
            $table->string('caption', 255)->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('journal_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 120)->unique();
            $table->string('category', 40)->default('Craft');
            $table->string('title', 190);
            $table->string('dek', 255)->nullable();
            $table->mediumText('body_html')->nullable();
            $table->unsignedInteger('craft_id')->nullable();
            $table->string('cover_path', 255)->nullable();
            $table->string('status', 40)->default('Published');
            $table->dateTime('published_at')->nullable();
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_desc', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_posts');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('celebrities');
        Schema::dropIfExists('press_items');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('product_stock');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_colours');
        Schema::dropIfExists('product_collection');
        Schema::dropIfExists('products');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('settings');
    }
};

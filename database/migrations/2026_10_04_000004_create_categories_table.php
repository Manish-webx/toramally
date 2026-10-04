<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->string('slug', 60)->unique();
            $table->text('description')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed initial default categories
        $defaultCategories = [
            ['name' => 'Men', 'slug' => 'men', 'description' => 'Handcrafted footwear and bespoke shoes for men.', 'sort' => 10, 'active' => true],
            ['name' => 'Women', 'slug' => 'women', 'description' => 'Fine heels, mules and crafted shoes for women.', 'sort' => 20, 'active' => true],
            ['name' => 'Everyday', 'slug' => 'everyday', 'description' => 'Handmade house slippers, loafers and essentials.', 'sort' => 30, 'active' => true],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Hand-patinated belts, wallets and atelier keepsakes.', 'sort' => 40, 'active' => true],
            ['name' => 'Service', 'slug' => 'service', 'description' => 'Shoe shine, polishing and restoration services.', 'sort' => 50, 'active' => true],
        ];

        foreach ($defaultCategories as $cat) {
            DB::table('categories')->insert(array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

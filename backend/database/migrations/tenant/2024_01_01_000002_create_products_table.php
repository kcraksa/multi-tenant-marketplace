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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('cost_per_item', 10, 2)->nullable();
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('track_inventory')->default(true);
            $table->boolean('continue_selling')->default(false);
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('weight_unit')->default('kg');
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'draft', 'archived'])->default('active');
            $table->boolean('featured')->default(false);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'featured']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

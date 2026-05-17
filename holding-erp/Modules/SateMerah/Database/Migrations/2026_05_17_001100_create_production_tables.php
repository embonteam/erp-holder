<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->foreignId('output_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('yield_quantity', 18, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['brand_id', 'code']);
        });

        Schema::create('recipe_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->timestamps();
        });

        Schema::create('production_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->unique();
            $table->string('status')->default('draft');
            $table->decimal('planned_quantity', 18, 4);
            $table->decimal('actual_quantity', 18, 4)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('finished_goods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->timestamps();
        });

        Schema::create('production_waste', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_waste');
        Schema::dropIfExists('finished_goods');
        Schema::dropIfExists('production_batches');
        Schema::dropIfExists('recipe_items');
        Schema::dropIfExists('recipes');
    }
};

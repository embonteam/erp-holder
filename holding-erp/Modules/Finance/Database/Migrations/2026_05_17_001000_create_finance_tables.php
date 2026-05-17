<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 18, 2);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('payables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 18, 2);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->string('paymentable_type');
            $table->unsignedBigInteger('paymentable_id');
            $table->decimal('amount', 18, 2);
            $table->string('method');
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->index(['paymentable_type', 'paymentable_id']);
        });

        Schema::create('taxes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('taxable_type');
            $table->unsignedBigInteger('taxable_id');
            $table->string('tax_type');
            $table->decimal('rate', 8, 4);
            $table->decimal('amount', 18, 2);
            $table->date('tax_date');
            $table->timestamps();
            $table->index(['taxable_type', 'taxable_id']);
        });

        Schema::create('cashflows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('direction');
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 18, 2);
            $table->timestamp('occurred_at');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashflows');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payables');
        Schema::dropIfExists('receivables');
    }
};

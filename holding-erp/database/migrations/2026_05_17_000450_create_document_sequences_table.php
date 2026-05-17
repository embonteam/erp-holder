<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table): void {
            $table->id();
            $table->string('document_type');
            $table->string('date_key', 8);
            $table->string('scope_key');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();
            $table->unique(['document_type', 'date_key', 'scope_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};

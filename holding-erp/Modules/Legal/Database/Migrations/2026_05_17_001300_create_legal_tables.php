<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('holding_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->string('counterparty');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('legal_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('holding_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('document_number')->nullable();
            $table->string('title');
            $table->string('document_type');
            $table->string('file_path');
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->timestamps();
        });

        Schema::create('expiry_reminders', function (Blueprint $table): void {
            $table->id();
            $table->string('remindable_type');
            $table->unsignedBigInteger('remindable_id');
            $table->timestamp('remind_at');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['remindable_type', 'remindable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expiry_reminders');
        Schema::dropIfExists('legal_documents');
        Schema::dropIfExists('contracts');
    }
};

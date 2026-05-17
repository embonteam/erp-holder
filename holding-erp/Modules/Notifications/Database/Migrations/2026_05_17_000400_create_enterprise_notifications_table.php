<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enterprise_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('severity')->default('info');
            $table->string('title');
            $table->text('message');
            $table->jsonb('payload')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
            $table->index(['type', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_notifications');
    }
};

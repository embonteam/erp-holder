<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('holding_id')->nullable()->after('role_id')->constrained()->nullOnDelete();
            $table->foreignId('holding_city_position_id')->nullable()->after('holding_id')->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('holding_city_position_id')->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('brand_id')->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('city_id')->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->index(['brand_id', 'city_id', 'branch_id', 'warehouse_id'], 'users_operational_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_operational_scope_idx');
            $table->dropConstrainedForeignId('warehouse_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('brand_id');
            $table->dropConstrainedForeignId('holding_city_position_id');
            $table->dropConstrainedForeignId('holding_id');
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['is_active', 'last_login_at']);
        });
    }
};

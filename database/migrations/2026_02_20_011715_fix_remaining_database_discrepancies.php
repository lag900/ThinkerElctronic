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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image_url');
            }
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('lms_point_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('lms_point_packages', 'sort_order') && Schema::hasColumn('lms_point_packages', 'order')) {
                $table->renameColumn('order', 'sort_order');
            }
            if (!Schema::hasColumn('lms_point_packages', 'active')) {
                $table->boolean('active')->default(true)->after('payment_link');
            }
            if (!Schema::hasColumn('lms_point_packages', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not strictly necessary for a fix-up migration but good practice
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'is_active']);
        });
    }
};

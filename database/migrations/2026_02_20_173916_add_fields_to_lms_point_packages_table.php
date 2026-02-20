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
        Schema::table('lms_point_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('lms_point_packages', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('lms_point_packages', 'active')) {
                $table->boolean('active')->default(true)->after('payment_link');
            }
            if (!Schema::hasColumn('lms_point_packages', 'sort_order') && Schema::hasColumn('lms_point_packages', 'order')) {
                $table->renameColumn('order', 'sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_point_packages', function (Blueprint $table) {
            $table->renameColumn('sort_order', 'order');
            $table->dropColumn(['name', 'active']);
        });
    }
};

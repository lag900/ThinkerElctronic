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
            $table->string('name')->nullable()->after('id');
            $table->boolean('active')->default(true)->after('payment_link');
            $table->renameColumn('order', 'sort_order');
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

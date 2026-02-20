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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'total_orders')) {
                $table->integer('total_orders')->default(0)->after('phone');
            }
            if (!Schema::hasColumn('customers', 'total_spent')) {
                $table->decimal('total_spent', 15, 2)->default(0)->after('total_orders');
            }
            if (!Schema::hasColumn('customers', 'last_order_at')) {
                $table->timestamp('last_order_at')->nullable()->after('total_spent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['total_orders', 'total_spent', 'last_order_at']);
        });
    }
};

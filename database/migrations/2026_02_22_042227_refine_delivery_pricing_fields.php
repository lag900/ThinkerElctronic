<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->decimal('default_delivery_price', 10, 2)->default(0)->after('name_ar');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->after('customer_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('default_delivery_price');
        });
    }
};

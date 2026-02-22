<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained()->onDelete('cascade');
            $table->string('name_en');
            $table->string('name_ar');
            $table->decimal('delivery_price', 10, 2);
            $table->decimal('express_price', 10, 2)->nullable();
            $table->string('delivery_days');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('free_delivery_over', 10, 2)->nullable();
            $table->boolean('express_enabled')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->string('governorate_name')->nullable();
            $table->string('area_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn(['area_id', 'governorate_name', 'area_name']);
        });
        Schema::dropIfExists('delivery_settings');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('governorates');
    }
};

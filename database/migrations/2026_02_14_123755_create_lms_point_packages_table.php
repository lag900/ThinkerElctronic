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
        if (!Schema::hasTable('lms_point_packages')) {
            Schema::create('lms_point_packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('points');
                $table->string('price'); // e.g. "25 جنيه"
                $table->string('payment_link')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_point_packages');
    }
};

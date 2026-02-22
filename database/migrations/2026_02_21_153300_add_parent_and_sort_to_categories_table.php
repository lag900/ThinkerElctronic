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
        Schema::table('categories', function (Blueprint $column) {
            $column->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $column->integer('sort_order')->default(0);
            $column->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $column) {
            $column->dropForeign(['parent_id']);
            $column->dropColumn(['parent_id', 'sort_order', 'is_active']);
        });
    }
};

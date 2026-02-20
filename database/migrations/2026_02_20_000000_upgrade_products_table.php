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
            $table->string('name_ar')->nullable()->after('name');
            $table->longText('description_ar')->nullable()->after('description');
            $table->longText('description_en')->nullable()->after('description_ar');
            $table->string('youtube_url')->nullable()->after('image_url');
        });

        Schema::create('product_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('value');
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });

        Schema::create('product_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('code');
            $table->string('language')->default('javascript'); // js, cpp, python, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_codes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_specs');
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'description_ar', 'description_en', 'youtube_url']);
        });
    }
};

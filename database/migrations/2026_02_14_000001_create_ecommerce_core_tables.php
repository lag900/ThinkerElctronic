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
        // Customers Table
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // Suppliers Table
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // Orders Table
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
                $table->string('order_number')->unique();
                $table->decimal('subtotal', 15, 2);
                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('total', 15, 2);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->decimal('total_profit', 15, 2)->default(0);
                $table->string('status')->default('pending');
                $table->text('shipping_address')->nullable();
                $table->timestamps();
            });
        }

        // Order Items Table
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity');
                $table->decimal('price', 15, 2);
                $table->decimal('cost', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // Topics Table
        if (!Schema::hasTable('topics')) {
            Schema::create('topics', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('content')->nullable();
                $table->string('image_url')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        // Inventory History Table
        if (!Schema::hasTable('inventory_history')) {
            Schema::create('inventory_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->integer('quantity_change');
                $table->string('type'); // entry, exit, etc.
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_history');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
    }
};

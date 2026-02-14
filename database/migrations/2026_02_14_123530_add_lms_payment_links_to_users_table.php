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
        Schema::table('users', function (Blueprint $table) {
            $table->string('lms_payment_25')->nullable()->after('remember_token');
            $table->string('lms_payment_100')->nullable()->after('lms_payment_25');
            $table->string('lms_payment_150')->nullable()->after('lms_payment_100');
            $table->string('lms_payment_200')->nullable()->after('lms_payment_150');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lms_payment_25', 'lms_payment_100', 'lms_payment_150', 'lms_payment_200']);
        });
    }
};

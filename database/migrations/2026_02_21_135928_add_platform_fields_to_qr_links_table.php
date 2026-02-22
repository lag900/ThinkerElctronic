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
        Schema::table('qr_links', function (Blueprint $table) {
            $table->string('platform')->nullable()->after('title');
            $table->string('custom_icon')->nullable()->after('icon');
            $table->timestamp('last_clicked_at')->nullable()->after('clicks');
        });
    }

    public function down(): void
    {
        Schema::table('qr_links', function (Blueprint $table) {
            $table->dropColumn(['platform', 'custom_icon', 'last_clicked_at']);
        });
    }
};

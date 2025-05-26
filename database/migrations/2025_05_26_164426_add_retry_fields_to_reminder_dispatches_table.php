<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('reminder_dispatches', function (Blueprint $table) {
            $table->unsignedInteger('retry_count')->default(0)->after('status');
            $table->timestamp('last_retry_at')->nullable()->after('retry_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_dispatches', function (Blueprint $table) {
            $table->dropColumn(['retry_count', 'last_retry_at']);
        });
    }
};

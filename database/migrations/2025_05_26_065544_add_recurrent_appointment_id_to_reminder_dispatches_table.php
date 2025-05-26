<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reminder_dispatches', function (Blueprint $table) {
            $table->foreignId('recurrent_appointment_id')
                ->nullable()
                ->after('appointment_id')
                ->constrained('recurrent_appointments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_dispatches', function (Blueprint $table) {
            $table->dropForeign(['recurrent_appointment_id']);
            $table->dropColumn('recurrent_appointment_id');
        });
    }
};

<?php

use App\Enums\Appointment\AppointmentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recurrent_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')
                ->constrained('appointments')
                ->onDelete('cascade');
            $table->timestamp('start_time');
            $table->string('timezone')->default('UTC');
            $table->string('status')->default(AppointmentStatusEnum::Scheduled->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrent_appointments');
    }
};

<?php

namespace App\Console\Commands;

use App\Enums\Appointment\AppointmentRecurrenceEnum;
use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\Appointment;
use App\Models\RecurrentAppointment;
use App\Models\ReminderDispatch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateRecurringAppointments extends Command
{
    protected $signature = 'appointments:generate-recurring';
    protected $description = 'Generate future recurring appointment instances based on recurrence and recurrence_ends_at';

    public function handle(): void
    {
        Appointment::with(['client', 'recurrences'])
            ->whereNotNull('recurrence')
            ->whereNotNull('recurrence_ends_at')
            ->where('recurrence_ends_at', '>', now())
            ->chunkById(100, function ($appointments) {
                foreach ($appointments as $appointment) {
                    $this->processAppointment($appointment);
                }
            });

        $this->info('Recurring appointments generated successfully.');
    }

    /**
     * @throws Throwable
     */
    private function processAppointment(Appointment $appointment): void
    {
        // Get the latest generated recurrence or fallback to appointment start time
        $latestRecurrence = $appointment->recurrences()->latest('start_time')->first();
        $currentDate = $latestRecurrence ? $latestRecurrence->start_time->copy() : $appointment->start_time->copy();

        $endDate = Carbon::parse($appointment->recurrence_ends_at);

        while (true) {
            $nextDate = $this->getNextDate($appointment, $currentDate);

            if ($nextDate->gt($endDate)) {
                break;
            }

            if (!$this->recurrenceExists($appointment, $nextDate)) {
                DB::transaction(function () use ($appointment, $nextDate) {
                    $recurrence = $appointment->recurrences()->create([
                        'start_time' => $nextDate,
                        'status' => $appointment->status,
                    ]);

                    $this->scheduleInstanceReminders($recurrence, $appointment);

                });
            }

            Log::info('Recurring appointments generated successfully.');

            $currentDate = $nextDate;
        }
    }


    private function getNextDate(Appointment $appointment, Carbon $currentDate): Carbon
    {
        return match ($appointment->recurrence) {
            AppointmentRecurrenceEnum::Weekly => $currentDate->copy()->addWeek(),
            AppointmentRecurrenceEnum::Monthly => $currentDate->copy()->addMonthNoOverflow(),
            AppointmentRecurrenceEnum::None => $currentDate->copy(),
            default => throw new \UnexpectedValueException('Unknown recurrence type: ' . $appointment->recurrence->value),
        };
    }

    private function recurrenceExists(Appointment $appointment, Carbon $date): bool
    {
        return $appointment->recurrences()
            ->whereDate('start_time', $date->toDateString())
            ->exists();
    }

    private function scheduleInstanceReminders(RecurrentAppointment $instance, Appointment $appointment): void
    {
        $offsets = config('reminders.offsets');

        foreach ((array)$offsets as $offset) {
            ReminderDispatch::create([
                'appointment_id' => $instance->appointment_id,
                'recurrent_appointment_id' => $instance->id,
                'scheduled_for' => $instance->start_time->copy()->subMinutes($offset),
                'status' => ReminderStatusEnum::Scheduled,
                'channel' => $appointment->client->prefers_sms
                    ? ReminderChannelEnum::Sms->value
                    : ReminderChannelEnum::Email->value,
            ]);
        }
    }
}

<?php

namespace App\Services\Appointment;

use App\Enums\Appointment\AppointmentStatusEnum;
use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\Appointment;
use App\Models\RecurrentAppointment;
use App\Models\Client;
use App\Models\ReminderDispatch;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class AppointmentService
{
    private ?Appointment $appointment;

    private array $reminderOffsets;

    public function __construct()
    {
        $this->reminderOffsets = config('reminders.offsets');
    }

    /**
     * Store appointment and create an initial instance if recurring.
     *
     * @throws Throwable
     */
    public function store(array $data): self
    {
        DB::beginTransaction();

        try {
            $client = Client::findOrFail($data['client_id']);
            $timezone = $client->timezone;

            // Parse and convert to UTC
            $startTimeUtc = Carbon::parse($data['start_time'], $timezone)->utc();

            $this->appointment = Appointment::create([
                'user_id' => auth()->id(),
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_time' => $startTimeUtc,
                'status' => AppointmentStatusEnum::Scheduled,
                'recurrence' => $data['recurrence'] ?? null,
                'recurrence_ends_at' => isset($data['recurrence_ends_at'])
                    ? Carbon::parse($data['recurrence_ends_at'], $timezone)->utc()
                    : null,
            ]);

            if ($this->appointment->recurrence) {
                $this->createRecurrentAppointmentInstance($startTimeUtc);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Create a recurrent appointment instance.
     */
    private function createRecurrentAppointmentInstance(Carbon $startTime): void
    {
        $this->appointment->recurrences()->create([
            'start_time' => $startTime,
            'status' => $this->appointment->status,
        ]);
    }

    /**
     * Schedule reminders for the appointment.
     * Decides internally whether to schedule for one-time or recurring.
     */
    public function scheduleReminders(): self
    {
        if ($this->appointment->recurrence) {
            // Recurring: schedule reminders for all recurrences (initial instance created on store)
            $this->appointment->load('recurrences');

            foreach ($this->appointment->recurrences as $recurrence) {
                $this->scheduleRemindersForInstance($recurrence);
            }
        } else {
            // One-time: schedule reminders directly for the appointment
            $this->scheduleRemindersForOneTimeAppointment();
        }

        return $this;
    }

    /**
     * Schedule reminders for a recurring appointment instance.
     */
    private function scheduleRemindersForInstance(RecurrentAppointment $instance): void
    {
        foreach ($this->reminderOffsets as $offset) {
            ReminderDispatch::create([
                'appointment_id' => $instance->appointment_id,
                'recurrent_appointment_id' => $instance->id,
                'scheduled_for' => $instance->start_time->copy()->subMinutes($offset),
                'status' => ReminderStatusEnum::Scheduled,
                'channel' => $this->appointment->client?->prefers_sms
                    ? ReminderChannelEnum::Sms->value
                    : ReminderChannelEnum::Email->value,
            ]);
        }
    }

    /**
     * Schedule reminders for one-time appointments (no instances).
     */
    private function scheduleRemindersForOneTimeAppointment(): void
    {
        foreach ($this->reminderOffsets as $offset) {
            ReminderDispatch::create([
                'appointment_id' => $this->appointment->id,
                'recurrent_appointment_id' => null,
                'scheduled_for' => $this->appointment->start_time->copy()->subMinutes($offset),
                'status' => ReminderStatusEnum::Scheduled,
                'channel' => $this->appointment->client?->prefers_sms
                    ? ReminderChannelEnum::Sms->value
                    : ReminderChannelEnum::Email->value,
            ]);
        }
    }

    /**
     * Get the stored appointment.
     */
    public function getAppointment(): Appointment
    {
        return $this->appointment;
    }

    public function getUpcomingAppointments($user): array|Collection
    {
        return Appointment::whereHas('client', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->ofStatus(AppointmentStatusEnum::Scheduled->value)
            ->where('start_time', '>=', Carbon::now())
            ->orderBy('start_time')
            ->get();
    }

    public function getPastAppointments($user): array|Collection
    {
        return Appointment::whereHas('client', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->ofStatus(AppointmentStatusEnum::notScheduled())
            ->where('start_time', '<', Carbon::now())
            ->orderBy('start_time', 'desc')
            ->get();
    }
}

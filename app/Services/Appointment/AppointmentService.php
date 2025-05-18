<?php

namespace App\Services\Appointment;

use App\Enums\Appointment\AppointmentStatusEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\ReminderDispatch;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentService
{
    private Appointment $appointment;

    public function store(array $data): self
    {
        $startTimeUtc = Carbon::parse($data['start_time'], Client::findOrFail($data['client_id'])->timezone)->utc();

        $this->appointment =
            Appointment::create([
                'user_id' => auth()->id(),
                'client_id' => $data['client_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'start_time' => $startTimeUtc,
                'status' => AppointmentStatusEnum::Scheduled,
            ]);

        return $this;
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

    public function scheduleReminder(): self
    {
        ReminderDispatch::create([
            'appointment_id' => $this->appointment->id,
            'scheduled_for' => $this->appointment->start_time->subMinutes(config('reminders.offset_minutes')),
            'status' => ReminderStatusEnum::Scheduled
        ]);

        return $this;
    }

    public function getAppointment(): Appointment
    {
        return $this->appointment;
    }


}

<?php

namespace App\Services\Appointment;

use App\Enums\Appointment\AppointmentStatusEnum;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentService
{
    public function store(array $data, int $userId): Appointment
    {
        return Appointment::create([
            'user_id' => $userId,
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_time' => $data['start_time'],
            'timezone' => $data['timezone'],
            'status' => AppointmentStatusEnum::Scheduled,
        ]);
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

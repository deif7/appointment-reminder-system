<?php

namespace App\Enums\Appointment;

enum AppointmentStatusEnum: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Canceled = 'canceled';
    case Missed = 'missed';

    public static function notScheduled(): array
    {
        return [
            self::Completed,
            self::Canceled,
            self::Missed,
        ];
    }
}

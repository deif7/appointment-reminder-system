<?php

namespace App\Enums\Appointment;

enum AppointmentRecurrenceEnum: string
{
    case None = 'none';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}

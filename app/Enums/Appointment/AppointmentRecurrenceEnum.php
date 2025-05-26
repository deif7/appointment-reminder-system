<?php

namespace App\Enums\Appointment;

enum AppointmentRecurrenceEnum: string
{
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}

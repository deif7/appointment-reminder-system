<?php

namespace App\Enums\ReminderDispatch;

enum ReminderStatusEnum: string
{
    case Scheduled = 'scheduled';
    case Sent = 'sent';
    case Failed = 'failed';
}

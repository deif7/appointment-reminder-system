<?php

namespace App\Enums\ReminderDispatch;

enum ReminderStatusEnum: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
}

<?php

namespace App\Enums\ReminderDispatch;

enum ReminderChannelEnum: string
{
    case Email = 'email';
    case Sms = 'sms';
}

<?php

namespace App\Http\Controllers;

use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\ReminderDispatch;

class ReminderController extends Controller
{
    public function scheduled()
    {
        return response()->json(
            ReminderDispatch::whereStatus(ReminderStatusEnum::Scheduled->value)
                ->whereNull('sent_at')
                ->get()
        );
    }

    public function sent()
    {
        return response()->json(
            ReminderDispatch::whereStatus(ReminderStatusEnum::Sent->value)
                ->whereNotNull('sent_at')
                ->get()
        );
    }
}

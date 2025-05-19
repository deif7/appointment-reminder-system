<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReminderDispatchResource;
use App\Models\Appointment;
use App\Models\ReminderDispatch;

class AdminController extends Controller
{
    public function appointmentStats()
    {
        return response()->json([
            'total_appointments' => Appointment::count(),
            'upcoming_appointments' => Appointment::where('start_time', '>', now())->count(),
            'sent_reminders' => ReminderDispatch::whereNotNull('sent_at')->count(),
            'pending_reminders' => ReminderDispatch::whereNull('sent_at')->count(),
        ]);
    }

    public function allReminders()
    {
        return ReminderDispatchResource::collection(ReminderDispatch::with(['appointment'])->get());
    }
}

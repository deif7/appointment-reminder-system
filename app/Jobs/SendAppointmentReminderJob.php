<?php


namespace App\Jobs;

use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\ReminderDispatch;
use Throwable;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function handle(): void
    {
        $now = now();

        ReminderDispatch::with(['appointment.client'])
            ->where('status', ReminderStatusEnum::Scheduled->value)
            ->whereNull('sent_at')
            ->whereBetween('scheduled_for', [$now->copy()->subSeconds(30), $now->copy()->addSeconds(30)])
            ->each(function (ReminderDispatch $reminder) {
                SendReminderNotificationJob::dispatch($reminder)->delay($reminder->scheduled_for);
            });
        Log::info('Appointment reminder job completed.');
    }

    protected function sendReminder(ReminderDispatch $reminder): void
    {
    }



}

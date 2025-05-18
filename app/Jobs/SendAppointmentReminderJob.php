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

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function handle(): void
    {
        ReminderDispatch::with(['appointment.client'])
            ->where('status', ReminderStatusEnum::Scheduled->value)
            ->whereNull('sent_at')
            ->where('scheduled_for', '<=', now())
            ->get()
            ->each(fn(ReminderDispatch $reminder) => $this->sendReminder($reminder));

        Log::info('Appointment reminder job completed.');
    }

    protected function sendReminder(ReminderDispatch $reminder): void
    {
        $appointment = $reminder->appointment;

        $client = $appointment->client;

        $reminder->channel === ReminderChannelEnum::Sms
            ? $this->logSmsMock($client, $appointment)
            : $client->notify(new AppointmentReminderNotification($appointment));

        $reminder->update([
            'status' => ReminderStatusEnum::Sent->value,
            'sent_at' => now(),
        ]);

        Log::info("Reminder sent for Appointment #{$appointment->id} to Client {$client->name}");
    }

    private function logSmsMock($client, $appointment): void
    {
        Log::info("Mock SMS to {$client->phone}: Upcoming Appointment - Title: {$appointment->title}, Starts At: {$appointment->start_time}. Please be prepared.");
    }

}

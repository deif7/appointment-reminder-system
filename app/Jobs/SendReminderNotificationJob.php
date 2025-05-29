<?php

namespace App\Jobs;

use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\ReminderDispatch;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ReminderDispatch $reminder)
    {

    }

    public function handle(): void
    {
        $reminder = $this->reminder;

        try {
            $appointment = $reminder->appointment;
            $client = $appointment->client;

            $reminder->channel === ReminderChannelEnum::Sms
                ? $this->logSmsMock($client, $appointment)
                : $client->notify(new AppointmentReminderNotification($appointment));

            // Reset retry info on successful send
            $reminder->update([
                'status' => ReminderStatusEnum::Sent->value,
                'sent_at' => now(),
                'retry_count' => 0,
                'last_retry_at' => null,
                'error_message' => null,
            ]);

            Log::info("Reminder sent for Appointment #{$appointment->id} to Client {$client->name}");
        } catch (Throwable $e) {
            $retryCount = $reminder->retry_count + 1;

            $updateData = [
                'retry_count' => $retryCount,
                'last_retry_at' => now(),
                'error_message' => $e->getMessage(),
            ];

            if ($retryCount >= config('reminders.max_retries', 3)) {
                $updateData['status'] = ReminderStatusEnum::Failed->value;
                Log::error("Reminder #{$reminder->id} failed after max retries ({$retryCount}): {$e->getMessage()}");
            } else {
                Log::warning("Reminder #{$reminder->id} failed, will retry (attempt {$retryCount}): {$e->getMessage()}");
            }

            $reminder->update($updateData);
        }

    }

    private function logSmsMock($client, $appointment): void
    {
        Log::info("Mock SMS to {$client->phone}: Upcoming Appointment - Title: {$appointment->title}, Starts At: {$appointment->start_time}. Please be prepared.");
    }
}

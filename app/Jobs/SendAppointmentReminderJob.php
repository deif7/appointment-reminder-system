<?php

namespace App\Jobs;

use App\Enums\ReminderDispatch\ReminderStatusEnum;
use Exception;
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
        $now = now();
        $processedCount = 0;

        try {
            $lastProcessedReminder = ReminderDispatch::where('status', ReminderStatusEnum::Sent->value)
                ->orderBy('scheduled_for', 'desc')
                ->first();

            $fromTime = $lastProcessedReminder
                ? $lastProcessedReminder->scheduled_for
                : $now->copy()->subMinutes(2);

            ReminderDispatch::with(['appointment.client'])
                ->where('status', ReminderStatusEnum::Scheduled->value)
                ->whereNull('sent_at')
                ->where('scheduled_for', '<=', $now)
                ->where('scheduled_for', '>', $fromTime)
                ->orderBy('scheduled_for')
                ->chunkById(50, function ($reminders) use (&$processedCount, $now) {
                    foreach ($reminders as $reminder) {
                        try {
                            SendReminderNotificationJob::dispatch($reminder);
                            $processedCount++;
                        } catch (Exception $e) {
                            Log::error("Failed to process reminder {$reminder->id}: " . $e->getMessage(), [
                                'reminder_id' => $reminder->id,
                                'scheduled_for' => $reminder->scheduled_for,
                                'job_start_time' => $now->toDateTimeString()
                            ]);
                            continue;
                        }
                    }
                });

            Log::info('Appointment reminder job completed', [
                'processed_count' => $processedCount,
                'from_time' => $fromTime,
                'to_time' => $now,
                'duration_seconds' => now()->diffInSeconds($now)
            ]);
        } catch (Exception $e) {
            Log::error("Appointment reminder job failed: " . $e->getMessage());
        }
    }
}

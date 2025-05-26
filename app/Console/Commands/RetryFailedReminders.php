<?php

namespace App\Console\Commands;

use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\ReminderDispatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedReminders extends Command
{
    protected $signature = 'reminders:retry-failed';

    protected $description = 'Retry sending failed reminders that have remaining retry attempts';

    public function handle(): int
    {
        $maxRetries = config('reminders.max_retries', 3);
        $retryDelayMinutes = config('reminders.retry_delay_minutes', 10);

        $retryCutoff = now()->subMinutes($retryDelayMinutes);

        $remindersToRetry = ReminderDispatch::where('status', ReminderStatusEnum::Failed->value)
            ->where('retry_count', '<', $maxRetries)
            ->where(function ($query) use ($retryCutoff) {
                $query->whereNull('last_retry_at')
                    ->orWhere('last_retry_at', '<=', $retryCutoff);
            })
            ->get();

        $count = $remindersToRetry->count();

        if ($count === 0) {
            $this->info('No reminders eligible for retry at this time.');
            return 0;
        }

        foreach ($remindersToRetry as $reminder) {
            $reminder->update(['status' => ReminderStatusEnum::Scheduled->value]);
            Log::info("Reminder #{$reminder->id} status reset to Scheduled for retry.");
        }

        $this->info("Reset {$count} failed reminders for retry.");

        return 0;
    }
}

<?php

return [
    'offsets' =>
        [
            'offset_minutes' => 30,
            'offset_hours' => 60,
        ],

    'max_retries' => env('REMINDERS_MAX_RETRIES', 3),
    'retry_delay_minutes' => env('REMINDERS_RETRY_DELAY_MINUTES', 10),

];

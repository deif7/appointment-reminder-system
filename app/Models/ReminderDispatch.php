<?php

namespace App\Models;

use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderDispatch extends Model
{

    protected $guarded = [];

    protected $casts = [
        'channel' => ReminderChannelEnum::class,
        'status' => ReminderStatusEnum::class,
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public static function countUpcoming(): int
    {
        return self::where('status', ReminderStatusEnum::Scheduled->value)->count();
    }

    public static function countSent(): int
    {
        return self::where('status', ReminderStatusEnum::Sent->value)->count();
    }

    public static function countFailed(): int
    {
        return self::where('status', ReminderStatusEnum::Failed->value)->count();
    }


}

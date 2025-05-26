<?php

namespace App\Models;

use App\Enums\Appointment\AppointmentRecurrenceEnum;
use App\Enums\Appointment\AppointmentStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'recurrence' => AppointmentRecurrenceEnum::class,
        'status' => AppointmentStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(ReminderDispatch::class);
    }

    public function recurrences(): HasMany
    {
        return $this->hasMany(RecurrentAppointment::class, 'appointment_id');
    }


    public function scopeOfStatus($query, array|string $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }

        return $query->where('status', $status);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurrentAppointment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function reminders(): HasMany
    {
        // Use the foreign key that links ReminderDispatch to RecurrentAppointment
        return $this->hasMany(ReminderDispatch::class, 'recurrent_appointment_id');
    }
}

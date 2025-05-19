<?php

namespace App\Http\Resources;

use App\Models\ReminderDispatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ReminderDispatch */
class ReminderDispatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'scheduled_for' => $this->scheduled_for,
            'sent_at' => $this->sent_at,
            'channel' => $this->channel,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'appointment_id' => $this->appointment_id,

            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
        ];
    }
}

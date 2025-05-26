<?php

namespace App\Http\Requests;

use App\Enums\Appointment\AppointmentStatusEnum;
use App\Traits\HandlesFailedValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentStatusUpdateRequest extends FormRequest
{
    use HandlesFailedValidationTrait;
    public function authorize(): bool
    {
        return auth()->check();
    }
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    AppointmentStatusEnum::Completed->value,
                    AppointmentStatusEnum::Canceled->value,
                    AppointmentStatusEnum::Missed->value,
                ]),
            ],
        ];
    }

}

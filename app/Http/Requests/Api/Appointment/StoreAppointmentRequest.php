<?php

namespace App\Http\Requests\Api\Appointment;

use App\Enums\Appointment\AppointmentRecurrenceEnum;
use App\Traits\HandlesFailedValidationTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    use HandlesFailedValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],

            'recurrence' => [
                'required_with:recurrence_ends_at',
                Rule::in(array_values(AppointmentRecurrenceEnum::cases()), 'value')
            ],
            'recurrence_ends_at' => [
                'required_with:recurrence',
                'date',
                'after_or_equal:start_time',
            ],
        ];
    }
}

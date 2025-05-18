<?php

namespace App\Http\Requests;

use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Traits\HandlesFailedValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReminderChannelToggleRequest extends FormRequest
{
    use HandlesFailedValidationTrait;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'channel' => [
                'required',
                Rule::in(array_column(ReminderChannelEnum::cases(), 'value')),
            ],
        ];
    }
}

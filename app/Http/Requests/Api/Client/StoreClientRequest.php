<?php

namespace App\Http\Requests\Api\Client;

use App\Traits\HandlesFailedValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    use HandlesFailedValidationTrait;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:clients,email'],
            'phone' => ['required', 'string'],
            'timezone' => ['sometimes', 'string'],
            'prefers_email' => ['nullable', 'bool'],
            'prefers_sms' => ['nullable', 'bool'],
        ];
    }
}

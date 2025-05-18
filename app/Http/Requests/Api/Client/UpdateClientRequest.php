<?php

namespace App\Http\Requests\Api\Client;

use App\Traits\HandlesFailedValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    use HandlesFailedValidationTrait;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'email' => ['some', 'email', 'unique:clients,email,' . $this->client->id],
            'phone' => ['sometimes', 'string'],
            'prefers_sms' => ['sometimes', 'bool'],
            'prefers_email' => ['sometimes', 'bool']
        ];
    }
}

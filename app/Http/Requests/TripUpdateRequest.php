<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TripUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'usernames' => ['nullable', 'array'],
            'usernames.*' => ['required', 'string', 'exists:users,username'],
        ];
    }

    public function messages(): array{
        return [
            'usernames.*.exists' => 'The username :input does not exist',
            'usernames.*.required' => 'Username cannot be empty',
        ];
    }
}

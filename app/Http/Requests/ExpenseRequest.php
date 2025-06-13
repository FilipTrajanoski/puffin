<?php

namespace App\Http\Requests;

use App\Enums\SplitMethod;
use App\Models\Expense;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $trip = $this->route('trip');
        return $trip->acceptedParticipants->contains($this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $trip = $this->route('trip');
        $participantIds = $trip->acceptedParticipants->pluck('id')->toArray();

        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'paid_by' => [
                'required',
                Rule::in($participantIds)
            ],
            'split_method' => ['required', Rule::in(SplitMethod::values())],
            'shares' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($participantIds) {
                    $shareKeys = array_keys($value);
                    sort($shareKeys);
                    sort($participantIds);

                    if ($shareKeys !== $participantIds) {
                        $fail('Shares must include all trip participants');
                    }
                }
            ],
            'shares.*' => 'numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'paid_by.in' => 'The payer must be a trip participant',
            'shares.*.min' => 'Share values cannot be negative',
        ];
    }
}

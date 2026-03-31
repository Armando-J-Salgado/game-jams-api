<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHandoverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'attachement' => ['required', 'string', 'url', 'active_url', 'regex:/^https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(?:\/\S*)?$/'],
            'is_delivered' => ['required', 'boolean'],
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'score' => ['nullable', 'integer', 'between:0,100'],
            'date_of_submission' => ['required', 'date', 'before_or_equal:today']
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHandoverRequest extends FormRequest
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
            'title' => ['sometimes','required', 'string', 'max:255'],
            'attachement' => ['sometimes','required', 'string', 'url', 'active_url', 'regex:/^https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(?:\/\S*)?$/'],
            'is_delivered' => ['sometimes','required', 'boolean'],
            'module_id' => ['sometimes','required', 'integer', 'exists:modules,id'],
            'team_id' => ['sometimes','required', 'integer', 'exists:teams,id'],
            'score' => ['sometimes','required', 'integer', 'between:0,100'],
            'date_of_submission' => ['sometimes','required', 'date', 'before_or_equal:today']
        ];
    }
}

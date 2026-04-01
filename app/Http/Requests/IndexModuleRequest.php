<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexModuleRequest extends FormRequest
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
     * @return array<string|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>['sometimes', 'string'],
            'due_date'=>['sometimes', Rule::date()->format('Y-m-d')],
            'competition_id'=>['sometimes', 'integer', 'exists:competitions,id'],
            'is_trashed'=>['sometimes', 'boolean'],
            'per_page'=>['sometimes', 'integer', 'gte:1'],
            'page'=>['sometimes', 'integer', 'gte: 1'],
        ];
    }
}

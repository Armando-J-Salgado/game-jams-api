<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCompetitionRequest extends FormRequest
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
            'name'=> ['sometimes', 'string'],
            'is_finished'=>['sometimes', 'boolean'],
            'start_date'=>['sometimes', Rule::date()->format('Y-m-d')],
            'end_date'=>['sometimes', Rule::date()->format('Y-m-d')],
            'per_page'=>['sometimes', 'integer', 'gt:0'],
            'is_trashed'=>['sometimes', 'boolean']
        ];
    }
}

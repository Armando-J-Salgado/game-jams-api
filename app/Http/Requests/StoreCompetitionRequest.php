<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompetitionRequest extends FormRequest
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
            'name'=>['required', 'string', 'max:255'],
            'description'=>['required', 'string', 'min:1', 'max:500'],
            'prize_information'=>['required', 'string', 'max:500', 'min:1'],
            'tools_information'=>['required', 'string', 'max:500', 'min:1'],
            'max_teams'=>['sometimes', 'integer', 'gt:0'],
            'start_date'=>['required', Rule::date()->format('Y-m-d')],
            'end_date'=>['required', Rule::date()->format('Y-m-d'), 'after:start_date'],
            'category_id'=>['required', 'exists:categories,id'],
        ];
    }
}

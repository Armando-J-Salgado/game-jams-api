<?php

namespace App\Http\Requests;

use App\Models\Competition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateModuleRequest extends FormRequest
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
        $competition = $this->route('module')->competition;
        $end_date = $competition->end_date;
        $start_date = $competition->start_date;
        return [
            'title'=>['sometimes', 'string', 'min:1', 'max:255'],
            'description'=>['sometimes', 'string', 'min:1', 'max:300'],
            'attachments'=>['sometimes', 'string', 'min:1', 'max:255'],
            'due_date'=>['sometimes', Rule::date()->format('Y-m-d'), Rule::when($competition !== null, ['after_or_equal:'.$start_date, 'before_or_equal:'.$end_date])],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $team = $this->route('team');
        $teamId = is_object($team) ? $team->id : ($team ?: 'NULL');

        return [
            'name' => 'sometimes|required|string|max:255|unique:teams,name,' . $teamId,
            'admin_id' => 'sometimes|required|exists:users,id',
            'max_members' => 'nullable|integer|min:1',
        ];
    }
}

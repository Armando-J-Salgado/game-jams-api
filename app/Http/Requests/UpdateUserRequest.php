<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('id');
        $user = $this->user();

        $baseRules = [
            'name' => ['sometimes', 'string', 'min:2', 'max:100'],
            'lastname' => ['sometimes', 'string', 'min:2', 'max:100'],
            'username' => ['sometimes', 'string', 'min:3', 'max:100', Rule::unique('users', 'username')->ignore($userId)],
        ];

        if (!$user || !$user->hasRole('administrador')) {
            return $baseRules;
        }

        return $baseRules + [
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'dui' => ['sometimes', 'string', 'regex:/^\d{8}-\d$/', Rule::unique('users', 'dui')->ignore($userId)],
            'role' => ['sometimes', 'string', 'in:administrador,organizador,lider,participante'],
        ];
    }
}

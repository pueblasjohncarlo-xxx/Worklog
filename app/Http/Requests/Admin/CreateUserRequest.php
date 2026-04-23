<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->role === User::ROLE_ADMIN;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'], // Removed unique check here, handled in controller
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,coordinator,supervisor,student,ojt_adviser'],
            'company_id' => ['required_if:role,supervisor', 'nullable', 'integer', 'exists:companies,id'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required_if' => 'A company is required when creating a supervisor account.',
            'company_id.exists' => 'The selected company is invalid.',
        ];
    }
}

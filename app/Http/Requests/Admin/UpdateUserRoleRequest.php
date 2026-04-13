<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->role === User::ROLE_ADMIN;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:admin,staff,coordinator,supervisor,student,ojt_adviser'],
        ];
    }
}

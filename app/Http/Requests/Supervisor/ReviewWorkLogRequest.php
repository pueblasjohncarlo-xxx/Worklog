<?php

namespace App\Http\Requests\Supervisor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ReviewWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->role === User::ROLE_SUPERVISOR;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected'],
            'grade' => ['nullable', 'string', 'max:10'],
            'reviewer_comment' => ['nullable', 'string'],
        ];
    }
}

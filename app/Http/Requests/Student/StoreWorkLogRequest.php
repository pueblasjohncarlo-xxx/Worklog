<?php

namespace App\Http\Requests\Student;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->role === User::ROLE_STUDENT;
    }

    public function rules(): array
    {
        return [
            'work_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0', 'max:24'],
            'description' => ['required', 'string'],
            'skills_applied' => ['nullable', 'string'],
            'reflection' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:doc,docx,ppt,pptx,pdf', 'max:10240'], // 10MB max
        ];
    }
}

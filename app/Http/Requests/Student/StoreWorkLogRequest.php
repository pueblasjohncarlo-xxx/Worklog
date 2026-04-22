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
            'type' => ['required', 'in:daily,weekly,monthly'],
            'work_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0', 'max:24'],

            // Template-based workflow: students upload a completed document instead of typing the report in the form.
            'attachment' => ['required', 'file', 'mimes:doc,docx,odt,pdf', 'max:51200'], // 50MB max

            // Keep backward compatibility with existing DB structure.
            'description' => ['nullable', 'string'],
            'skills_applied' => ['nullable', 'string'],
            'reflection' => ['nullable', 'string'],
        ];
    }
}

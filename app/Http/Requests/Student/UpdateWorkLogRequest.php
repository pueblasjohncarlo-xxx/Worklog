<?php

namespace App\Http\Requests\Student;

use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->role === User::ROLE_STUDENT;
    }

    public function rules(): array
    {
        $id = $this->route('worklog') ?? $this->route('workLog');
        $workLog = is_numeric($id) ? WorkLog::find((int) $id) : null;

        $isAccomplishmentEntry = $workLog && $workLog->time_in === null && $workLog->time_out === null;
        $needsAttachment = $isAccomplishmentEntry && empty($workLog->attachment_path);

        return [
            'work_date' => ['required', 'date'],
            'time_in' => ['nullable', 'string'],
            'time_out' => ['nullable', 'string'],
            'hours' => ['required', 'numeric', 'min:0', 'max:24'],

            // Template-based workflow: text fields are optional; attachment is required for accomplishment entries.
            'description' => ['nullable', 'string'],
            'skills_applied' => ['nullable', 'string'],
            'reflection' => ['nullable', 'string'],
            'attachment' => [Rule::requiredIf($needsAttachment), 'nullable', 'file', 'mimes:doc,docx,odt,pdf', 'max:51200'],
        ];
    }
}

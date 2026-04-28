<?php

namespace App\Http\Requests\Student;

use App\Models\User;
use App\Models\WorkLog;
use Carbon\Carbon;
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
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'hours' => ['required', 'numeric', 'min:0', 'max:24'],
            'submit_after_save' => ['nullable', 'boolean'],

            // Template-based workflow: text fields are optional; attachment is required for accomplishment entries.
            'description' => ['nullable', 'string'],
            'skills_applied' => ['nullable', 'string'],
            'reflection' => ['nullable', 'string'],
            'attachment' => [Rule::requiredIf($needsAttachment), 'nullable', 'file', 'mimes:doc,docx,odt,pdf', 'max:51200'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->route('worklog') ?? $this->route('workLog');
            $workLog = is_numeric($id) ? WorkLog::find((int) $id) : null;

            if (! $workLog) {
                return;
            }

            $isAttendanceStyleLog = $workLog->time_in !== null || $workLog->time_out !== null;
            if (! $isAttendanceStyleLog) {
                return;
            }

            $timeIn = $this->input('time_in');
            $timeOut = $this->input('time_out');

            if (blank($timeIn) && blank($timeOut)) {
                $validator->errors()->add('time_in', 'Time in is required for hours log entries.');

                return;
            }

            if (blank($timeIn) && filled($timeOut)) {
                $validator->errors()->add('time_in', 'Time in is required when time out is provided.');

                return;
            }

            if (filled($timeIn) && filled($timeOut)) {
                try {
                    $start = Carbon::createFromFormat('H:i', (string) $timeIn);
                    $end = Carbon::createFromFormat('H:i', (string) $timeOut);
                } catch (\Throwable) {
                    return;
                }

                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $hours = $start->floatDiffInHours($end);

                if ($hours <= 0 || $hours > 24) {
                    $validator->errors()->add('time_out', 'Time out must produce a valid work duration between 0 and 24 hours.');
                }
            }
        });
    }
}

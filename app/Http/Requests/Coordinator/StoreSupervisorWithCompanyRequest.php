<?php

namespace App\Http\Requests\Coordinator;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupervisorWithCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === User::ROLE_COORDINATOR;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Supervisor rules
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'position_title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Company toggle
            'create_company' => ['boolean'],
        ];

        // Conditional company rules
        if ($this->boolean('create_company')) {
            $rules = array_merge($rules, [
                'company_name' => ['required', 'string', 'max:255', 'unique:companies,name'],
                'company_industry' => ['required', 'string', 'max:255'],
                'company_address' => ['required', 'string', 'max:255'],
                'company_city' => ['required', 'string', 'max:100'],
                'company_state' => ['required', 'string', 'max:100'],
                'company_postal_code' => ['required', 'string', 'max:20'],
                'company_country' => ['required', 'string', 'max:100'],
                'company_contact_person' => ['required', 'string', 'max:255'],
                'company_contact_email' => ['required', 'email', 'max:255'],
                'company_contact_phone' => ['required', 'string', 'max:20'],
            ]);
        }

        return $rules;
    }
}

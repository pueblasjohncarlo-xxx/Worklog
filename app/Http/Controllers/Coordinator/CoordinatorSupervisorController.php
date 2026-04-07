<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\StoreSupervisorWithCompanyRequest;
use App\Models\Company;
use App\Models\SupervisorProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CoordinatorSupervisorController extends Controller
{
    /**
     * Show the form for creating a new supervisor.
     */
    public function create(): View
    {
        return view('coordinator.supervisors.create');
    }

    /**
     * Store a newly created supervisor in storage.
     */
    public function store(StoreSupervisorWithCompanyRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // 1. Create Supervisor User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_SUPERVISOR,
            ]);

            $companyId = null;

            // 2. Optional Company Creation
            if ($request->boolean('create_company')) {
                $company = Company::create([
                    'name' => $request->company_name,
                    'industry' => $request->company_industry,
                    'address' => $request->company_address,
                    'city' => $request->company_city,
                    'state' => $request->company_state,
                    'postal_code' => $request->company_postal_code,
                    'country' => $request->company_country,
                    'contact_person' => $request->company_contact_person,
                    'contact_email' => $request->company_contact_email,
                    'contact_phone' => $request->company_contact_phone,
                ]);
                $companyId = $company->id;
            }

            // 3. Create Supervisor Profile
            SupervisorProfile::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'position_title' => $request->position_title,
                'department' => $request->department,
                'phone' => $request->phone,
            ]);

            DB::commit();

            return redirect()->route('coordinator.dashboard')
                ->with('status', 'Supervisor created successfully'.($companyId ? ' and linked to the new company.' : '.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the supervisor: '.$e->getMessage()]);
        }
    }
}

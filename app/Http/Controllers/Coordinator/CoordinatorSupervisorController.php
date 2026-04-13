<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinator\StoreSupervisorWithCompanyRequest;
use App\Models\Company;
use App\Models\OjtAdviserProfile;
use App\Models\SupervisorProfile;
use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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
        $plainPassword = (string) $request->password;
        $role = (string) $request->input('role', User::ROLE_SUPERVISOR);

        try {
            DB::beginTransaction();

            // 1. Create Supervisor or OJT Adviser user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($plainPassword),
                'encrypted_password' => Crypt::encryptString($plainPassword),
                'role' => $role,
            ];

            if (Schema::hasColumn('users', 'is_approved')) {
                $userData['is_approved'] = true;
            }

            if (Schema::hasColumn('users', 'has_requested_account')) {
                $userData['has_requested_account'] = true;
            }

            if (Schema::hasColumn('users', 'status')) {
                $userData['status'] = 'approved';
            }

            if (Schema::hasColumn('users', 'approved_at')) {
                $userData['approved_at'] = now();
            }

            if (Schema::hasColumn('users', 'approved_by')) {
                $userData['approved_by'] = $request->user()->id;
            }

            $user = User::create($userData);

            $companyId = null;

            // 2. Optional Company Creation
            if ($role === User::ROLE_SUPERVISOR && $request->boolean('create_company')) {
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

            // 3. Create role profile
            if ($role === User::ROLE_SUPERVISOR) {
                SupervisorProfile::create([
                    'user_id' => $user->id,
                    'company_id' => $companyId,
                    'position_title' => $request->position_title,
                    'department' => $request->department,
                    'phone' => $request->phone,
                ]);
            } else {
                OjtAdviserProfile::create([
                    'user_id' => $user->id,
                    'department' => $request->department,
                    'phone' => $request->phone,
                    'address' => null,
                ]);
            }

            DB::commit();

            $warning = null;

            // 4. Send account invitation (fails gracefully)
            try {
                $user->notify(new AccountInvitationNotification($plainPassword, (string) $request->user()->name));
            } catch (\Throwable $mailError) {
                $warning = ' Account was created, but invitation email could not be sent.';
            }

            return redirect()->route('coordinator.dashboard')
                ->with('status', ucfirst(str_replace('_', ' ', $role)).' account created successfully'.($companyId ? ' and linked to the new company.' : '.').($warning ?? ''));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the supervisor: '.$e->getMessage()]);
        }
    }
}

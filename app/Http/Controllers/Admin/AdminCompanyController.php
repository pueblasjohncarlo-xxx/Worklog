<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Models\Company;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminCompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::with('defaultSupervisor')->orderBy('name')->get();
        $supervisors = User::where('role', User::ROLE_SUPERVISOR)->orderBy('name')->get();

        // For the map view exactly like coordinator dashboard
        $companiesForMap = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'industry' => $company->industry,
                'address' => $company->address,
                'latitude' => $company->latitude,
                'longitude' => $company->longitude,
            ];
        });

        return view('admin.companies.index', compact('companies', 'supervisors', 'companiesForMap'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        Company::create($request->validated());

        return redirect()->route('admin.companies.index')
            ->with('status', 'Company successfully created with its associated data.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('status', 'Company record deleted successfully.');
    }
}

<x-coordinator-layout>
    <x-slot name="header">
        Company Directory
    </x-slot>

    @php
        $totalCompanies = $companies->count();
        $companiesWithStudents = $companies->filter(fn ($company) => $company->assignments->where('status', 'active')->count() > 0)->count();
        $incompleteCompanies = $companies->filter(function ($company) {
            return blank($company->contact_person)
                || blank($company->contact_email)
                || blank($company->contact_phone)
                || blank($company->address)
                || blank($company->city)
                || blank($company->country);
        })->count();
        $activeCompanies = $companiesWithStudents;
        $industryOptions = $companies
            ->pluck('industry')
            ->filter()
            ->map(fn ($industry) => trim((string) $industry))
            ->filter()
            ->unique()
            ->sort()
            ->values();
    @endphp

    <div class="space-y-6" x-data="phAddress()">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Companies</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCompanies }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Active Partners</p>
                <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $activeCompanies }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">With Assigned Students</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $companiesWithStudents }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Incomplete Profiles</p>
                <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $incompleteCompanies }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="p-6 lg:p-8 text-gray-900 dark:text-gray-100 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold">Register New Partner Company</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create complete, searchable company records for supervisor assignment and deployment automation.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-3 text-sm text-red-700 dark:text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="rounded-lg border border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20 p-3 text-sm text-green-700 dark:text-green-300">
                        @if (session('status') === 'company-created')
                            Company added successfully.
                        @elseif (session('status') === 'company-updated')
                            Company updated successfully.
                        @elseif (session('status') === 'company-deleted')
                            Company deleted successfully.
                        @else
                            {{ session('status') }}
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('coordinator.companies.store') }}" class="space-y-6">
                    @csrf

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Company Basics</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="name" class="block text-sm font-medium">Company Name <span class="text-red-500">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" minlength="2" placeholder="e.g. Timex Technologies" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="industry" class="block text-sm font-medium">Industry</label>
                                <input id="industry" name="industry" type="text" value="{{ old('industry') }}" maxlength="255" list="industry-options" placeholder="e.g. Information Technology" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <datalist id="industry-options">
                                    @foreach($industryOptions as $industryOption)
                                        <option value="{{ $industryOption }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Contact Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="contact_person" class="block text-sm font-medium">Contact Person</label>
                                <input id="contact_person" name="contact_person" type="text" value="{{ old('contact_person') }}" maxlength="255" placeholder="Full name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="contact_email" class="block text-sm font-medium">Contact Email</label>
                                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}" maxlength="255" placeholder="name@company.com" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="contact_phone" class="block text-sm font-medium">Contact Phone</label>
                            <div class="relative rounded-md shadow-sm mt-1">
                                <div class="absolute inset-y-0 left-0 flex items-center">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 sm:text-sm h-full" x-text="dialCode || '+--'"></span>
                                </div>
                                <input id="contact_phone" name="contact_phone" type="text" x-model="phoneNumber" maxlength="50" pattern="[0-9\s\-\+\(\)]{7,50}" placeholder="912 345 6789" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-16">
                            </div>
                        </div>
                    </div>

                    <details class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-5" @if(old('country') || old('address') || old('city') || old('state') || old('postal_code')) open @endif>
                        <summary class="cursor-pointer text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Address Details (Optional)</summary>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="space-y-1 relative">
                                <label for="country" class="block text-sm font-medium">Country</label>
                                <input id="country" name="country" type="text" x-model="country" @input="searchCountry()" @focus="searchCountry()" @click.away="showCountryList = false" autocomplete="off" placeholder="Type country..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="showCountryList && filteredCountries.length > 0" class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="c in filteredCountries" :key="c.name">
                                        <div @click="selectCountry(c)" class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm flex items-center gap-3">
                                            <img :src="c.flag" alt="" class="w-6 h-4 object-cover rounded-sm border border-gray-200">
                                            <span x-text="c.name"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-1 relative">
                                <label for="address" class="block text-sm font-medium">Street Address</label>
                                <input id="address" name="address" type="text" x-model="address" @input="searchAddress()" @focus="searchAddress()" @click.away="showAddressList = false" autocomplete="off" placeholder="Start typing address..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="showAddressList && filteredAddresses.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="a in filteredAddresses" :key="a.display_name">
                                        <div @click="selectAddress(a)" class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm border-b border-gray-100 dark:border-gray-600 last:border-0">
                                            <div class="font-medium text-gray-800 dark:text-gray-200" x-text="a.name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="a.display_name"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="space-y-1 relative">
                                <label for="city" class="block text-sm font-medium">City</label>
                                <input id="city" name="city" type="text" x-model="city" @input="searchCity()" @focus="searchCity()" @click.away="showCityList = false" autocomplete="off" placeholder="Type city..." :disabled="!country" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="showCityList && filteredCities.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="c in filteredCities" :key="c.name">
                                        <div @click="selectCity(c)" class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm" x-text="c.name"></div>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-1 relative">
                                <label for="state" class="block text-sm font-medium" x-text="stateLabel">State / Province</label>
                                <input id="state" name="state" type="text" x-model="barangay" @input="searchBarangay()" @focus="searchBarangay()" @click.away="showBarangayList = false" autocomplete="off" :placeholder="'Type ' + stateLabel.toLowerCase() + '...'" :disabled="!city && isPhilippines" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="showBarangayList && filteredBarangays.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="b in filteredBarangays" :key="b">
                                        <div @click="selectBarangay(b)" class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm" x-text="b"></div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium">Postal Code</label>
                                <input id="postal_code" name="postal_code" type="text" x-model="postalCode" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </details>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-xs font-semibold uppercase tracking-wide text-white hover:bg-indigo-700">Add Company</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="p-6 lg:p-8 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="font-semibold text-lg">Partner Company Directory</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400"><span id="visibleCompaniesCount">0</span> companies shown</p>
                </div>

                @if ($companies->isEmpty())
                    <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No companies yet. Add your first partner company using the form above.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <input id="companySearch" type="text" placeholder="Search company, location, contact..." class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <select id="industryFilter" class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Industries</option>
                            @foreach($industryOptions as $industryOption)
                                <option value="{{ strtolower($industryOption) }}">{{ $industryOption }}</option>
                            @endforeach
                        </select>
                        <select id="studentsFilter" class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">All Student Loads</option>
                            <option value="with">With Assigned Students</option>
                            <option value="none">No Assigned Students</option>
                        </select>
                        <select id="sortBy" class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="name_asc">Sort: Name (A-Z)</option>
                            <option value="name_desc">Sort: Name (Z-A)</option>
                            <option value="students_desc">Sort: Most Students</option>
                            <option value="students_asc">Sort: Least Students</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Industry</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="companiesTableBody" class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($companies as $company)
                                    @php
                                        $activeStudents = $company->assignments->where('status', 'active');
                                        $studentCount = $activeStudents->count();
                                        $hasIncompleteProfile = blank($company->contact_person)
                                            || blank($company->contact_email)
                                            || blank($company->contact_phone)
                                            || blank($company->address)
                                            || blank($company->city)
                                            || blank($company->country);

                                        $assignedSupervisors = $company->supervisorProfiles
                                            ->pluck('user.name')
                                            ->filter()
                                            ->unique()
                                            ->values();

                                        $assignedAdvisers = $company->assignments
                                            ->pluck('ojtAdviser.name')
                                            ->filter()
                                            ->unique()
                                            ->values();

                                        $searchBlob = strtolower(implode(' ', [
                                            $company->name,
                                            $company->industry,
                                            $company->city,
                                            $company->state,
                                            $company->country,
                                            $company->contact_person,
                                            $company->contact_email,
                                        ]));
                                    @endphp

                                    <tr
                                        class="company-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        data-company-id="{{ $company->id }}"
                                        data-name="{{ strtolower($company->name) }}"
                                        data-industry="{{ strtolower((string) $company->industry) }}"
                                        data-students="{{ $studentCount }}"
                                        data-search="{{ $searchBlob }}"
                                        x-data="{ editing: false }"
                                    >
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <template x-if="!editing">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</div>
                                            </template>
                                            <template x-if="editing">
                                                <input form="company-update-{{ $company->id }}" type="text" name="name" value="{{ $company->name }}" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                            </template>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <template x-if="!editing">
                                                <span>{{ $company->industry ?: 'Unspecified' }}</span>
                                            </template>
                                            <template x-if="editing">
                                                <input form="company-update-{{ $company->id }}" type="text" name="industry" value="{{ $company->industry }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                            </template>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-2">
                                                @if($studentCount > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Active Partner</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300">No Deployment</span>
                                                @endif

                                                @if($hasIncompleteProfile)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Incomplete Profile</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">{{ $studentCount }} students</span>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <template x-if="!editing">
                                                <span>{{ $company->city ?: '-' }}, {{ $company->country ?: '-' }}</span>
                                            </template>
                                            <template x-if="editing">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input form="company-update-{{ $company->id }}" type="text" name="city" value="{{ $company->city }}" placeholder="City" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                                    <input form="company-update-{{ $company->id }}" type="text" name="country" value="{{ $company->country }}" placeholder="Country" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                                </div>
                                            </template>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <template x-if="!editing">
                                                <div>
                                                    <div class="text-xs">{{ $company->contact_person ?: '-' }}</div>
                                                    <div class="text-[10px] text-gray-400">{{ $company->contact_email ?: 'No email' }}</div>
                                                </div>
                                            </template>
                                            <template x-if="editing">
                                                <div class="space-y-2">
                                                    <input form="company-update-{{ $company->id }}" type="text" name="contact_person" value="{{ $company->contact_person }}" placeholder="Contact person" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                                    <input form="company-update-{{ $company->id }}" type="email" name="contact_email" value="{{ $company->contact_email }}" placeholder="Contact email" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                                </div>
                                            </template>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <form id="company-update-{{ $company->id }}" method="POST" action="{{ route('coordinator.companies.update', $company) }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                            </form>

                                            <div class="flex items-center gap-2">
                                                <button type="button" class="px-2 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors" onclick="toggleCompanyDetails({{ $company->id }})">View</button>

                                                <button type="button" x-show="!editing" @click="editing = true" class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">Edit</button>
                                                <button type="button" x-show="editing" @click="$el.closest('tr').querySelector('form[id^=\'company-update-\']').submit()" class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700 hover:bg-green-200 transition-colors">Save</button>
                                                <button type="button" x-show="editing" @click="editing = false" class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">Cancel</button>

                                                <form method="POST" action="{{ route('coordinator.companies.destroy', $company) }}" onsubmit="return confirm('Delete this company? This is blocked if linked to deployments or supervisors.');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700 hover:bg-red-200 transition-colors">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="company-details-{{ $company->id }}" class="company-details hidden bg-gray-50/70 dark:bg-gray-900/20" data-parent="{{ $company->id }}">
                                        <td colspan="7" class="px-4 py-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Full Location</p>
                                                    <p class="mt-1 text-gray-700 dark:text-gray-200">{{ $company->address ?: 'No address recorded' }}</p>
                                                    <p class="text-gray-500 dark:text-gray-400">{{ $company->city ?: '-' }}, {{ $company->state ?: '-' }}, {{ $company->postal_code ?: '-' }}, {{ $company->country ?: '-' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Contact Details</p>
                                                    <p class="mt-1 text-gray-700 dark:text-gray-200">Person: {{ $company->contact_person ?: 'Not set' }}</p>
                                                    <p class="text-gray-500 dark:text-gray-400">Email: {{ $company->contact_email ?: 'Not set' }}</p>
                                                    <p class="text-gray-500 dark:text-gray-400">Phone: {{ $company->contact_phone ?: 'Not set' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Assignments</p>
                                                    <p class="mt-1 text-gray-700 dark:text-gray-200">Supervisors: {{ $assignedSupervisors->isNotEmpty() ? $assignedSupervisors->implode(', ') : 'None' }}</p>
                                                    <p class="text-gray-500 dark:text-gray-400">Advisers: {{ $assignedAdvisers->isNotEmpty() ? $assignedAdvisers->implode(', ') : 'None' }}</p>
                                                    <p class="text-gray-500 dark:text-gray-400">Students: {{ $activeStudents->pluck('student.name')->filter()->unique()->values()->implode(', ') ?: 'None' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        function phAddress() {
            return {
                country: '',
                city: '',
                barangay: '',
                postalCode: '',
                
                address: '',
                phoneNumber: '',
                dialCode: '',
                
                // Data Sources
                allCountries: [],
                cities: [], // Will hold both cities and municipalities
                barangays: [],
                
                // Filtered Lists
                filteredCountries: [],
                filteredCities: [],
                filteredBarangays: [],
                filteredAddresses: [],
                
                // State
                showCountryList: false,
                showCityList: false,
                showBarangayList: false,
                showAddressList: false,
                selectedCityData: null,
                isLoadingCities: false,
                isLoadingBarangays: false,
                zipCodes: {}, // Map of City Name -> Zip Code

                get isPhilippines() {
                    return this.country.toLowerCase() === 'philippines';
                },

                get stateLabel() {
                    return this.isPhilippines ? 'Barangay' : 'State / Province';
                },

                async init() {
                    // Load Countries
                    try {
                        const res = await fetch('https://restcountries.com/v3.1/all?fields=name,flags,idd');
                        const data = await res.json();
                        this.allCountries = data.map(c => ({
                            name: c.name.common,
                            flag: c.flags.svg,
                            dialCode: (c.idd.root || '') + (c.idd.suffixes ? c.idd.suffixes[0] : '')
                        })).sort((a, b) => a.name.localeCompare(b.name));
                    } catch (e) {
                        console.error("Failed to load countries", e);
                    }

                    // Load PH Zip Codes (same as before)
                    this.loadZipCodes();
                },

                async loadZipCodes() {
                    // Static robust list for major PH cities
                    this.zipCodes = {
                        "Manila": "1000", "Caloocan City": "1400", "Las Piñas City": "1740", "Makati City": "1200", 
                        "Malabon City": "1470", "Mandaluyong City": "1550", "Marikina City": "1800", "Muntinlupa City": "1770", 
                        "Navotas City": "1485", "Parañaque City": "1700", "Pasay City": "1300", "Pasig City": "1600", 
                        "Quezon City": "1100", "San Juan City": "1500", "Taguig City": "1630", "Valenzuela City": "1440",
                        "Cebu City": "6000", "Lapu-Lapu City": "6015", "Mandaue City": "6014", "Talisay City": "6045",
                        "Davao City": "8000", "Zamboanga City": "7000", "Cagayan de Oro City": "9000", "Iloilo City": "5000",
                        "Baguio City": "2600", "Bacolod City": "6100", "Angeles City": "2009", "General Santos City": "9500",
                        "Butuan City": "8600", "Iligan City": "9200", "Tarlac City": "2300", "Olongapo City": "2200",
                        "Batangas City": "4200", "Lipa City": "4217", "San Pablo City": "4000", "Lucena City": "4301",
                        "Puerto Princesa City": "5300", "Naga City": "4400", "Legazpi City": "4500", "Tacloban City": "6500",
                        "Ormoc City": "6541", "Catbalogan City": "6700", "Tagbilaran City": "6300", "Dumaguete City": "6200",
                        "Roxas City": "5800", "Cotabato City": "9600", "Marawi City": "9700", "Isabela City": "7300"
                    };
                },

                // --- Country Logic ---
                searchCountry() {
                    // Show full list if empty, otherwise filter
                    if (this.country === '') {
                        this.filteredCountries = this.allCountries.slice(0, 50); // Show top 50
                    } else {
                        const q = this.country.toLowerCase();
                        this.filteredCountries = this.allCountries
                            .filter(c => c.name.toLowerCase().startsWith(q))
                            .slice(0, 50);
                        // Auto-pick when there's a single clear match or exact match
                        const exact = this.allCountries.find(c => c.name.toLowerCase() === q);
                        if (exact) {
                            this.selectCountry(exact);
                        } else if (this.filteredCountries.length === 1 && q.length >= 3) {
                            this.selectCountry(this.filteredCountries[0]);
                        }
                    }
                    this.showCountryList = true;
                },

                async selectCountry(countryData) {
                    this.country = countryData.name;
                    this.dialCode = countryData.dialCode; // Set dial code
                    this.showCountryList = false;
                    
                    // Reset Dependent Fields
                    this.city = '';
                    this.barangay = '';
                    this.postalCode = '';
                    this.cities = [];
                    this.filteredCities = [];
                    this.barangays = [];
                    
                    if (this.isPhilippines) {
                        this.loadPHCities();
                    } else {
                        // Load Global Cities for Selected Country
                        this.loadGlobalCities(countryData.name);
                    }
                },

                // --- City Logic ---
                async loadPHCities() {
                    this.isLoadingCities = true;
                    try {
                        const [citiesRes, munisRes] = await Promise.all([
                            fetch('https://psgc.gitlab.io/api/cities.json'),
                            fetch('https://psgc.gitlab.io/api/municipalities.json')
                        ]);
                        const citiesRaw = await citiesRes.json();
                        const municipalitiesRaw = await munisRes.json();
                        const norm = (item, isCity) => {
                            let n = item.name || '';
                            n = n.replace(/\s*\(.*?\)\s*/g, ''); // remove parentheses like "(Opon)"
                            n = n.replace(/^City of\s+/i, '');   // "City of Cebu" -> "Cebu"
                            n = n.replace(/^Municipality of\s+/i, ''); // "Municipality of ..." -> "..."
                            if (isCity && !/\bCity\b$/i.test(n)) n = n + ' City';
                            return { ...item, name: n };
                        };
                        const cities = citiesRaw.map(c => norm(c, true));
                        const municipalities = municipalitiesRaw.map(m => norm(m, false));
                        this.cities = [...cities, ...municipalities].sort((a, b) => a.name.localeCompare(b.name));
                    } catch (error) {
                        console.error("Failed to load PH cities:", error);
                    } finally {
                        this.isLoadingCities = false;
                    }
                },

                async loadGlobalCities(countryName) {
                    this.isLoadingCities = true;
                    try {
                        // Use CountriesNow API for global cities
                        const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ country: countryName })
                        });
                        const data = await res.json();
                        if (!data.error) {
                            this.cities = data.data.map(c => ({ name: c })); // Standardize format
                        } else {
                            this.cities = [];
                        }
                    } catch (error) {
                        console.error("Failed to load global cities:", error);
                        this.cities = [];
                    } finally {
                        this.isLoadingCities = false;
                    }
                },

                searchCity() {
                    // Lazy-load cities if user typed country manually (without clicking)
                    if (this.cities.length === 0 && this.country) {
                        if (this.isPhilippines) {
                            this.loadPHCities();
                        } else {
                            this.loadGlobalCities(this.country);
                        }
                    }
                    if (this.city === '') {
                        // Optional: Show some default cities if list is loaded?
                        // For global, the list might be huge, so maybe just show top 50 if loaded
                        if (this.cities.length > 0) {
                             this.filteredCities = this.cities.slice(0, 50);
                             this.showCityList = true;
                        } else {
                             this.filteredCities = [];
                             this.showCityList = false;
                        }
                        return;
                    }
                    
                    // Filter from loaded list (PH or Global)
                    this.filteredCities = this.cities
                        .filter(c => c.name.toLowerCase().startsWith(this.city.toLowerCase()))
                        .slice(0, 50);
                    this.showCityList = true;
                },

                async selectCity(cityData) {
                    this.city = cityData.name;
                    this.selectedCityData = cityData;
                    this.showCityList = false;
                    
                    if (this.isPhilippines) {
                        // Auto-fill Zip for PH
                        if (this.zipCodes[cityData.name]) {
                            this.postalCode = this.zipCodes[cityData.name];
                        } else {
                            let cleanName = cityData.name.replace(" City", "").replace("Municipality of ", "");
                            this.postalCode = this.zipCodes[cleanName] || '';
                        }
                        
                        // Load Barangays
                        this.loadPHBarangays(cityData);
                    } else {
                        // Global: Try to load states/provinces if possible, or just allow manual entry
                        // CountriesNow has a separate endpoint for states, but linking city to state is tricky without knowing the state first.
                        // Flow: Country -> State -> City is usually better for global API structure.
                        // But user asked for City -> State auto-fill if possible?
                        // For now, we allow manual State/Province entry for global.
                        this.barangay = ''; 
                    }
                },

                // --- Barangay/State Logic ---
                async loadPHBarangays(cityData) {
                    this.barangay = '';
                    this.barangays = [];
                    this.isLoadingBarangays = true;
                    try {
                        let endpoint = '';
                        if (cityData.cityClass) {
                            endpoint = `https://psgc.gitlab.io/api/cities/${cityData.code}/barangays.json`;
                        } else {
                            endpoint = `https://psgc.gitlab.io/api/municipalities/${cityData.code}/barangays.json`;
                        }
                        const res = await fetch(endpoint);
                        this.barangays = await res.json();
                        this.barangays.sort((a, b) => a.name.localeCompare(b.name));
                    } catch (error) {
                        console.error("Failed to fetch barangays:", error);
                    } finally {
                        this.isLoadingBarangays = false;
                    }
                },

                searchBarangay() {
                    if (this.barangay === '' && !this.isPhilippines) return; // Allow manual typing for non-PH

                    if (this.isPhilippines) {
                        if (this.barangay === '') {
                             // Show all if empty and focused (optional, but requested "type first letter")
                             // Actually user said "type first letter... show list".
                             // But for barangays, seeing the whole list on click is nicer.
                             this.filteredBarangays = this.barangays.slice(0, 100);
                        } else {
                             this.filteredBarangays = this.barangays
                                .filter(b => b.name.toLowerCase().startsWith(this.barangay.toLowerCase()));
                        }
                        this.showBarangayList = true;
                    } else {
                        this.showBarangayList = false;
                    }
                },

                selectBarangay(barangayData) {
                    this.barangay = barangayData.name;
                    this.showBarangayList = false;
                },

                // --- Street Address Logic (Nominatim) ---
                async searchAddress() {
                    if (this.address.length < 3) {
                        this.filteredAddresses = [];
                        this.showAddressList = false;
                        return;
                    }

                    // Build query context
                    let query = this.address;
                    let context = '';
                    if (this.city) context += ', ' + this.city;
                    if (this.country) context += ', ' + this.country;
                    
                    try {
                        // Use Nominatim API (OpenStreetMap)
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + context)}&format=json&addressdetails=1&limit=5`);
                        const data = await res.json();
                        
                        this.filteredAddresses = data.map(item => ({
                            name: item.name || item.display_name.split(',')[0],
                            display_name: item.display_name,
                            full_data: item
                        }));
                        
                        this.showAddressList = true;
                    } catch (e) {
                        console.error("Address search failed", e);
                    }
                },

                selectAddress(addressData) {
                    // When selecting an address, we can optionally auto-fill other fields if they are empty
                    this.address = addressData.name;
                    
                    // Optional: Smart fill if City/Country/Postcode are missing
                    const addr = addressData.full_data.address;
                    if (!this.city && (addr.city || addr.town || addr.village)) {
                         this.city = addr.city || addr.town || addr.village;
                    }
                    if (!this.postalCode && addr.postcode) {
                        this.postalCode = addr.postcode;
                    }
                    
                    this.showAddressList = false;
                }
            }
        }

        function toggleCompanyDetails(companyId) {
            const detailsRow = document.getElementById(`company-details-${companyId}`);
            if (!detailsRow) {
                return;
            }

            detailsRow.classList.toggle('hidden');
        }

        function applyCompanyDirectoryFilters() {
            const tbody = document.getElementById('companiesTableBody');
            if (!tbody) {
                return;
            }

            const search = (document.getElementById('companySearch')?.value || '').toLowerCase().trim();
            const industry = (document.getElementById('industryFilter')?.value || '').toLowerCase();
            const studentsFilter = document.getElementById('studentsFilter')?.value || 'all';
            const sortBy = document.getElementById('sortBy')?.value || 'name_asc';

            const rows = Array.from(tbody.querySelectorAll('tr.company-row'));
            const rowPairs = rows.map((row) => {
                const companyId = row.dataset.companyId;
                const details = document.getElementById(`company-details-${companyId}`);
                return { row, details };
            });

            rowPairs.sort((a, b) => {
                const aName = a.row.dataset.name || '';
                const bName = b.row.dataset.name || '';
                const aStudents = Number(a.row.dataset.students || 0);
                const bStudents = Number(b.row.dataset.students || 0);

                if (sortBy === 'name_desc') {
                    return bName.localeCompare(aName);
                }
                if (sortBy === 'students_desc') {
                    return bStudents - aStudents;
                }
                if (sortBy === 'students_asc') {
                    return aStudents - bStudents;
                }
                return aName.localeCompare(bName);
            });

            rowPairs.forEach(({ row, details }) => {
                tbody.appendChild(row);
                if (details) {
                    tbody.appendChild(details);
                }
            });

            let visibleCount = 0;

            rowPairs.forEach(({ row, details }) => {
                const rowSearch = row.dataset.search || '';
                const rowIndustry = row.dataset.industry || '';
                const rowStudents = Number(row.dataset.students || 0);

                const matchesSearch = search === '' || rowSearch.includes(search);
                const matchesIndustry = industry === '' || rowIndustry === industry;
                const matchesStudents = studentsFilter === 'all'
                    || (studentsFilter === 'with' && rowStudents > 0)
                    || (studentsFilter === 'none' && rowStudents === 0);

                const show = matchesSearch && matchesIndustry && matchesStudents;
                row.style.display = show ? '' : 'none';

                if (!show && details) {
                    details.style.display = 'none';
                    details.classList.add('hidden');
                }

                if (show) {
                    visibleCount += 1;
                }
            });

            const visibleCountElement = document.getElementById('visibleCompaniesCount');
            if (visibleCountElement) {
                visibleCountElement.textContent = String(visibleCount);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const controls = ['companySearch', 'industryFilter', 'studentsFilter', 'sortBy'];
            controls.forEach((id) => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', applyCompanyDirectoryFilters);
                    element.addEventListener('change', applyCompanyDirectoryFilters);
                }
            });

            applyCompanyDirectoryFilters();
        });
    </script>
</x-coordinator-layout>

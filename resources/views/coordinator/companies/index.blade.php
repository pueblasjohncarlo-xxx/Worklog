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

        $companyFormInitial = [
            'country' => old('country', ''),
            'city' => old('city', ''),
            'state' => old('state', ''),
            'postal_code' => old('postal_code', ''),
            'address' => old('address', ''),
        ];
    @endphp

    <div class="space-y-6" x-data='phAddress(@json($companyFormInitial))'>
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
                        <input type="hidden" name="address" :value="formatAddressForSubmit()">
                        <input type="hidden" name="city" :value="city">
                        <input type="hidden" name="state" :value="isPhilippines ? province : stateText">

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
                                <label for="street_address" class="block text-sm font-medium">Street Address</label>
                                <input id="street_address" type="text" x-model="streetAddress" @input="searchAddress()" @focus="searchAddress()" @click.away="showAddressList = false" autocomplete="off" placeholder="Start typing address..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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

                        <div x-show="isPhilippines" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="province_create" class="block text-sm font-medium">Province</label>
                                <select id="province_create" x-model="province" @change="onCreateProvinceChange()" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select province</option>
                                    <template x-for="prov in provinces" :key="prov.code">
                                        <option :value="prov.name" x-text="prov.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label for="city_create_ph" class="block text-sm font-medium">City / Municipality</label>
                                <select id="city_create_ph" x-model="city" @change="onCreateCityChange()" :disabled="!province" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select city/municipality</option>
                                    <template x-for="cityOption in createCityOptions" :key="cityOption.code">
                                        <option :value="cityOption.name" x-text="cityOption.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label for="barangay_create" class="block text-sm font-medium">Barangay</label>
                                <select id="barangay_create" x-model="barangay" :disabled="!city" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select barangay</option>
                                    <template x-for="barangayOption in createBarangayOptions" :key="barangayOption">
                                        <option :value="barangayOption" x-text="barangayOption"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div x-show="!isPhilippines" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="space-y-1 relative">
                                <label for="city_create_manual" class="block text-sm font-medium">City</label>
                                <input id="city_create_manual" type="text" x-model="city" @input="searchCity()" @focus="searchCity()" @click.away="showCityList = false" autocomplete="off" placeholder="Type city..." :disabled="!country" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="showCityList && filteredCities.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="c in filteredCities" :key="c.name">
                                        <div @click="selectCity(c)" class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm" x-text="c.name"></div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label for="state_create_manual" class="block text-sm font-medium">State / Province</label>
                                <input id="state_create_manual" type="text" x-model="stateText" autocomplete="off" placeholder="Type state/province..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
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
                                        data-company-name="{{ e($company->name) }}"
                                        data-company-industry="{{ e((string) $company->industry) }}"
                                        data-company-country="{{ e((string) $company->country) }}"
                                        data-company-state="{{ e((string) $company->state) }}"
                                        data-company-city="{{ e((string) $company->city) }}"
                                        data-company-address="{{ e((string) $company->address) }}"
                                        data-company-postal="{{ e((string) $company->postal_code) }}"
                                        data-company-contact-person="{{ e((string) $company->contact_person) }}"
                                        data-company-contact-email="{{ e((string) $company->contact_email) }}"
                                        data-company-contact-phone="{{ e((string) $company->contact_phone) }}"
                                        data-update-url="{{ route('coordinator.companies.update', $company) }}"
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

                                                <button type="button" class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors" onclick="openCompanyEditModal({{ $company->id }})">Edit</button>
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

        <div id="companyEditModal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-gray-900/60" onclick="closeCompanyEditModal()"></div>
            <div class="relative mx-auto mt-6 w-[95%] max-w-5xl rounded-xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700 max-h-[92vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between sticky top-0 bg-white dark:bg-gray-800 z-10">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Company</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update missing fields and keep company records complete and deployment-ready.</p>
                    </div>
                    <button type="button" onclick="closeCompanyEditModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Close</button>
                </div>

                <form id="companyEditForm" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div id="companyEditMissing" class="hidden rounded-lg border border-amber-200 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20 p-3 text-sm text-amber-800 dark:text-amber-300"></div>

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Basic Company Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="edit_name" class="block text-sm font-medium">Company Name <span class="text-red-500">*</span></label>
                                <input id="edit_name" name="name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="edit_industry" class="block text-sm font-medium">Industry</label>
                                <input id="edit_industry" name="industry" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Location Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="edit_country" class="block text-sm font-medium">Country</label>
                                <input id="edit_country" name="country" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" oninput="handleEditCountryInput()">
                            </div>
                            <div>
                                <label for="edit_street" class="block text-sm font-medium">Street Address</label>
                                <input id="edit_street" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Building / street / block">
                            </div>
                        </div>

                        <div id="editPHLocationWrap" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 hidden">
                            <div>
                                <label for="edit_province" class="block text-sm font-medium">Province</label>
                                <select id="edit_province" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="handleEditProvinceChange()">
                                    <option value="">Select province</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_city_ph" class="block text-sm font-medium">City / Municipality</label>
                                <select id="edit_city_ph" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="handleEditCityChange()">
                                    <option value="">Select city/municipality</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_barangay" class="block text-sm font-medium">Barangay</label>
                                <select id="edit_barangay" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select barangay</option>
                                </select>
                            </div>
                        </div>

                        <div id="editNonPHLocationWrap" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="edit_city_manual" class="block text-sm font-medium">City</label>
                                <input id="edit_city_manual" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="edit_state_manual" class="block text-sm font-medium">State / Province</label>
                                <input id="edit_state_manual" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="edit_postal_code" class="block text-sm font-medium">Postal Code</label>
                            <input id="edit_postal_code" name="postal_code" type="text" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Contact Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="edit_contact_person" class="block text-sm font-medium">Contact Person</label>
                                <input id="edit_contact_person" name="contact_person" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="edit_contact_email" class="block text-sm font-medium">Contact Email</label>
                                <input id="edit_contact_email" name="contact_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="edit_contact_phone" class="block text-sm font-medium">Contact Phone</label>
                                <input id="edit_contact_phone" name="contact_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. +63 912 345 6789">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="edit_hidden_city" name="city">
                    <input type="hidden" id="edit_hidden_state" name="state">
                    <input type="hidden" id="edit_hidden_address" name="address">

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" onclick="closeCompanyEditModal()" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700 text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Save Company Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function phAddress(initial = {}) {
            return {
            country: initial.country || '',
            city: initial.city || '',
            barangay: '',
            province: initial.state || '',
            stateText: initial.state || '',
            postalCode: initial.postal_code || '',
                
            streetAddress: '',
                phoneNumber: '',
                dialCode: '',
                
                // Data Sources
                allCountries: [],
            provinces: [],
            phCities: [],
                cities: [], // Will hold both cities and municipalities
                barangays: [],
            createCityOptions: [],
            createBarangayOptions: [],
                
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

                formatAddressForSubmit() {
                    if (this.isPhilippines) {
                        const street = (this.streetAddress || '').trim();
                        const brgy = (this.barangay || '').trim();
                        if (brgy && street) {
                            return `Brgy: ${brgy} | ${street}`;
                        }
                        if (brgy) {
                            return `Brgy: ${brgy}`;
                        }
                        return street;
                    }

                    return (this.streetAddress || '').trim();
                },

                parseAddressFromStored(rawAddress) {
                    const raw = (rawAddress || '').trim();
                    if (!raw) {
                        return { street: '', barangay: '' };
                    }

                    const match = raw.match(/^Brgy:\s*(.*?)\s*\|\s*(.*)$/i);
                    if (match) {
                        return {
                            barangay: (match[1] || '').trim(),
                            street: (match[2] || '').trim(),
                        };
                    }

                    return { street: raw, barangay: '' };
                },

                async init() {
                    const parsedAddress = this.parseAddressFromStored(initial.address || '');
                    this.streetAddress = parsedAddress.street;
                    this.barangay = parsedAddress.barangay;

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

                    if (this.isPhilippines) {
                        await this.loadPHGeography();
                    }
                },

                async loadPHGeography() {
                    try {
                        const [provincesRes, citiesRes, munisRes] = await Promise.all([
                            fetch('https://psgc.gitlab.io/api/provinces.json'),
                            fetch('https://psgc.gitlab.io/api/cities.json'),
                            fetch('https://psgc.gitlab.io/api/municipalities.json'),
                        ]);

                        const provincesRaw = await provincesRes.json();
                        const citiesRaw = await citiesRes.json();
                        const municipalitiesRaw = await munisRes.json();

                        const normalizeName = (name, isCity) => {
                            let n = name || '';
                            n = n.replace(/\s*\(.*?\)\s*/g, '');
                            n = n.replace(/^City of\s+/i, '');
                            n = n.replace(/^Municipality of\s+/i, '');
                            if (isCity && !/\bCity\b$/i.test(n)) {
                                n += ' City';
                            }
                            return n;
                        };

                        this.provinces = provincesRaw
                            .map((province) => ({ code: province.code, name: province.name }))
                            .sort((a, b) => a.name.localeCompare(b.name));

                        const normalizedCities = citiesRaw.map((city) => ({
                            code: city.code,
                            provinceCode: city.provinceCode,
                            name: normalizeName(city.name, true),
                            type: 'city',
                        }));

                        const normalizedMunicipalities = municipalitiesRaw.map((municipality) => ({
                            code: municipality.code,
                            provinceCode: municipality.provinceCode,
                            name: normalizeName(municipality.name, false),
                            type: 'municipality',
                        }));

                        this.phCities = [...normalizedCities, ...normalizedMunicipalities].sort((a, b) => a.name.localeCompare(b.name));

                        if (this.province) {
                            this.onCreateProvinceChange();
                        }
                        if (this.city) {
                            await this.onCreateCityChange();
                        }
                    } catch (error) {
                        console.error('Failed to load Philippines geography data:', error);
                    }
                },

                onCreateProvinceChange() {
                    const selectedProvince = this.provinces.find((province) => province.name === this.province);
                    const provinceCode = selectedProvince ? selectedProvince.code : null;
                    this.createCityOptions = provinceCode
                        ? this.phCities.filter((city) => city.provinceCode === provinceCode)
                        : [];

                    if (!this.createCityOptions.some((city) => city.name === this.city)) {
                        this.city = '';
                        this.barangay = '';
                        this.createBarangayOptions = [];
                    }
                },

                async onCreateCityChange() {
                    const selectedCity = this.createCityOptions.find((city) => city.name === this.city);

                    if (!selectedCity) {
                        this.createBarangayOptions = [];
                        this.barangay = '';
                        return;
                    }

                    if (this.zipCodes[selectedCity.name]) {
                        this.postalCode = this.zipCodes[selectedCity.name];
                    } else {
                        const cleanName = selectedCity.name.replace(' City', '').replace('Municipality of ', '');
                        this.postalCode = this.zipCodes[cleanName] || this.postalCode;
                    }

                    try {
                        const endpoint = selectedCity.type === 'city'
                            ? `https://psgc.gitlab.io/api/cities/${selectedCity.code}/barangays.json`
                            : `https://psgc.gitlab.io/api/municipalities/${selectedCity.code}/barangays.json`;
                        const response = await fetch(endpoint);
                        const data = await response.json();
                        this.createBarangayOptions = data
                            .map((barangay) => barangay.name)
                            .sort((a, b) => a.localeCompare(b));

                        if (!this.createBarangayOptions.includes(this.barangay)) {
                            this.barangay = '';
                        }
                    } catch (error) {
                        console.error('Failed to load barangays for create form:', error);
                        this.createBarangayOptions = [];
                    }
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
                    this.province = '';
                    this.stateText = '';
                    this.postalCode = '';
                    this.cities = [];
                    this.filteredCities = [];
                    this.barangays = [];
                    this.createCityOptions = [];
                    this.createBarangayOptions = [];
                    
                    if (this.isPhilippines) {
                        this.loadPHGeography();
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
                    if ((this.streetAddress || '').length < 3) {
                        this.filteredAddresses = [];
                        this.showAddressList = false;
                        return;
                    }

                    // Build query context
                    let query = this.streetAddress;
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
                    this.streetAddress = addressData.name;
                    
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

            const editForm = document.getElementById('companyEditForm');
            if (editForm) {
                editForm.addEventListener('submit', prepareEditFormPayload);
            }
        });

        const companyEditState = {
            cacheLoaded: false,
            provinces: [],
            cities: [],
            selectedCityOptions: [],
            preloaded: {
                state: '',
                city: '',
                barangay: '',
            },
        };

        function parseStoredAddress(rawAddress) {
            const raw = String(rawAddress || '').trim();
            const match = raw.match(/^Brgy:\s*(.*?)\s*\|\s*(.*)$/i);
            if (match) {
                return {
                    barangay: (match[1] || '').trim(),
                    street: (match[2] || '').trim(),
                };
            }

            return {
                barangay: '',
                street: raw,
            };
        }

        function formatAddressForStorage(street, barangay) {
            const cleanStreet = String(street || '').trim();
            const cleanBarangay = String(barangay || '').trim();
            if (cleanBarangay && cleanStreet) {
                return `Brgy: ${cleanBarangay} | ${cleanStreet}`;
            }
            if (cleanBarangay) {
                return `Brgy: ${cleanBarangay}`;
            }
            return cleanStreet;
        }

        async function ensurePHGeoCache() {
            if (companyEditState.cacheLoaded) {
                return;
            }

            const [provincesRes, citiesRes, munisRes] = await Promise.all([
                fetch('https://psgc.gitlab.io/api/provinces.json'),
                fetch('https://psgc.gitlab.io/api/cities.json'),
                fetch('https://psgc.gitlab.io/api/municipalities.json'),
            ]);

            const provincesRaw = await provincesRes.json();
            const citiesRaw = await citiesRes.json();
            const municipalitiesRaw = await munisRes.json();

            const normalizeName = (name, isCity) => {
                let n = name || '';
                n = n.replace(/\s*\(.*?\)\s*/g, '');
                n = n.replace(/^City of\s+/i, '');
                n = n.replace(/^Municipality of\s+/i, '');
                if (isCity && !/\bCity\b$/i.test(n)) {
                    n += ' City';
                }
                return n;
            };

            companyEditState.provinces = provincesRaw
                .map((province) => ({ code: province.code, name: province.name }))
                .sort((a, b) => a.name.localeCompare(b.name));

            const normalizedCities = citiesRaw.map((city) => ({
                code: city.code,
                provinceCode: city.provinceCode,
                name: normalizeName(city.name, true),
                type: 'city',
            }));

            const normalizedMunicipalities = municipalitiesRaw.map((municipality) => ({
                code: municipality.code,
                provinceCode: municipality.provinceCode,
                name: normalizeName(municipality.name, false),
                type: 'municipality',
            }));

            companyEditState.cities = [...normalizedCities, ...normalizedMunicipalities].sort((a, b) => a.name.localeCompare(b.name));
            companyEditState.cacheLoaded = true;
        }

        function closeCompanyEditModal() {
            const modal = document.getElementById('companyEditModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function openCompanyEditModal(companyId) {
            const row = document.querySelector(`tr.company-row[data-company-id="${companyId}"]`);
            if (!row) {
                return;
            }

            const form = document.getElementById('companyEditForm');
            const modal = document.getElementById('companyEditModal');
            if (!form || !modal) {
                return;
            }

            const parsedAddress = parseStoredAddress(row.dataset.companyAddress || '');

            form.setAttribute('action', row.dataset.updateUrl || '');
            document.getElementById('edit_name').value = row.dataset.companyName || '';
            document.getElementById('edit_industry').value = row.dataset.companyIndustry || '';
            document.getElementById('edit_country').value = row.dataset.companyCountry || '';
            document.getElementById('edit_postal_code').value = row.dataset.companyPostal || '';
            document.getElementById('edit_contact_person').value = row.dataset.companyContactPerson || '';
            document.getElementById('edit_contact_email').value = row.dataset.companyContactEmail || '';
            document.getElementById('edit_contact_phone').value = row.dataset.companyContactPhone || '';
            document.getElementById('edit_street').value = parsedAddress.street || '';
            document.getElementById('edit_city_manual').value = row.dataset.companyCity || '';
            document.getElementById('edit_state_manual').value = row.dataset.companyState || '';

            companyEditState.preloaded.state = row.dataset.companyState || '';
            companyEditState.preloaded.city = row.dataset.companyCity || '';
            companyEditState.preloaded.barangay = parsedAddress.barangay || '';

            showEditMissingFields(row);
            handleEditCountryInput(true);

            modal.classList.remove('hidden');
        }

        function showEditMissingFields(row) {
            const missing = [];
            if (!row.dataset.companyContactPerson) missing.push('Contact person');
            if (!row.dataset.companyContactEmail) missing.push('Contact email');
            if (!row.dataset.companyContactPhone) missing.push('Contact phone');
            if (!row.dataset.companyAddress) missing.push('Street/barangay');
            if (!row.dataset.companyCity) missing.push('City');
            if (!row.dataset.companyState) missing.push('State/Province');
            if (!row.dataset.companyCountry) missing.push('Country');

            const panel = document.getElementById('companyEditMissing');
            if (!panel) {
                return;
            }

            if (missing.length === 0) {
                panel.classList.add('hidden');
                panel.textContent = '';
                return;
            }

            panel.classList.remove('hidden');
            panel.textContent = `Incomplete profile fields: ${missing.join(', ')}.`;
        }

        async function handleEditCountryInput(isPreload = false) {
            const country = (document.getElementById('edit_country')?.value || '').trim().toLowerCase();
            const isPhilippines = country === 'philippines';
            const phWrap = document.getElementById('editPHLocationWrap');
            const nonPHWrap = document.getElementById('editNonPHLocationWrap');

            if (!phWrap || !nonPHWrap) {
                return;
            }

            phWrap.classList.toggle('hidden', !isPhilippines);
            nonPHWrap.classList.toggle('hidden', isPhilippines);

            if (!isPhilippines) {
                return;
            }

            await ensurePHGeoCache();

            const provinceSelect = document.getElementById('edit_province');
            if (!provinceSelect) {
                return;
            }

            provinceSelect.innerHTML = '<option value="">Select province</option>';
            companyEditState.provinces.forEach((province) => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });

            if (isPreload && companyEditState.preloaded.state) {
                const matchedProvince = companyEditState.provinces.find((province) => province.name.toLowerCase() === companyEditState.preloaded.state.toLowerCase());
                if (matchedProvince) {
                    provinceSelect.value = matchedProvince.code;
                }
            }

            await handleEditProvinceChange(isPreload);
        }

        async function handleEditProvinceChange(isPreload = false) {
            const provinceCode = document.getElementById('edit_province')?.value || '';
            const citySelect = document.getElementById('edit_city_ph');
            if (!citySelect) {
                return;
            }

            companyEditState.selectedCityOptions = provinceCode
                ? companyEditState.cities.filter((city) => city.provinceCode === provinceCode)
                : [];

            citySelect.innerHTML = '<option value="">Select city/municipality</option>';
            companyEditState.selectedCityOptions.forEach((city) => {
                const option = document.createElement('option');
                option.value = city.code;
                option.textContent = city.name;
                citySelect.appendChild(option);
            });

            if (isPreload && companyEditState.preloaded.city) {
                const matchedCity = companyEditState.selectedCityOptions.find((city) => city.name.toLowerCase() === companyEditState.preloaded.city.toLowerCase());
                if (matchedCity) {
                    citySelect.value = matchedCity.code;
                }
            }

            await handleEditCityChange(isPreload);
        }

        async function handleEditCityChange(isPreload = false) {
            const cityCode = document.getElementById('edit_city_ph')?.value || '';
            const barangaySelect = document.getElementById('edit_barangay');
            if (!barangaySelect) {
                return;
            }

            barangaySelect.innerHTML = '<option value="">Select barangay</option>';

            const selectedCity = companyEditState.selectedCityOptions.find((city) => city.code === cityCode);
            if (!selectedCity) {
                return;
            }

            try {
                const endpoint = selectedCity.type === 'city'
                    ? `https://psgc.gitlab.io/api/cities/${selectedCity.code}/barangays.json`
                    : `https://psgc.gitlab.io/api/municipalities/${selectedCity.code}/barangays.json`;

                const response = await fetch(endpoint);
                const data = await response.json();
                const barangays = data.map((barangay) => barangay.name).sort((a, b) => a.localeCompare(b));

                barangays.forEach((barangay) => {
                    const option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = barangay;
                    barangaySelect.appendChild(option);
                });

                if (isPreload && companyEditState.preloaded.barangay) {
                    const matched = barangays.find((barangay) => barangay.toLowerCase() === companyEditState.preloaded.barangay.toLowerCase());
                    if (matched) {
                        barangaySelect.value = matched;
                    }
                }
            } catch (error) {
                console.error('Failed to load barangays for edit form:', error);
            }
        }

        function prepareEditFormPayload() {
            const isPhilippines = (document.getElementById('edit_country')?.value || '').trim().toLowerCase() === 'philippines';
            const street = document.getElementById('edit_street')?.value || '';

            if (isPhilippines) {
                const provinceCode = document.getElementById('edit_province')?.value || '';
                const cityCode = document.getElementById('edit_city_ph')?.value || '';
                const barangay = document.getElementById('edit_barangay')?.value || '';

                const provinceName = companyEditState.provinces.find((province) => province.code === provinceCode)?.name || '';
                const cityName = companyEditState.selectedCityOptions.find((city) => city.code === cityCode)?.name || '';

                document.getElementById('edit_hidden_state').value = provinceName;
                document.getElementById('edit_hidden_city').value = cityName;
                document.getElementById('edit_hidden_address').value = formatAddressForStorage(street, barangay);
            } else {
                const manualState = document.getElementById('edit_state_manual')?.value || '';
                const manualCity = document.getElementById('edit_city_manual')?.value || '';
                document.getElementById('edit_hidden_state').value = manualState;
                document.getElementById('edit_hidden_city').value = manualCity;
                document.getElementById('edit_hidden_address').value = String(street || '').trim();
            }
        }
    </script>
</x-coordinator-layout>

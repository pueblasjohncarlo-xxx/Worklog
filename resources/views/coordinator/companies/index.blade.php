<x-coordinator-layout>
    <x-slot name="header">
        Company Directory
    </x-slot>

    <div class="space-y-6" x-data="phAddress()">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Register New Partner Company</h3>
                </div>
                @if ($errors->any())
                    <div class="text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('coordinator.companies.store') }}"
                    class="space-y-4"
                >
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="name" class="block text-sm font-medium">
                                Name
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label for="industry" class="block text-sm font-medium">
                                Industry
                            </label>
                            <input
                                id="industry"
                                name="industry"
                                type="text"
                                value="{{ old('industry') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1 relative">
                            <label for="country" class="block text-sm font-medium">
                                Country
                            </label>
                            <input
                                id="country"
                                name="country"
                                type="text"
                                x-model="country"
                                @input="searchCountry()"
                                @focus="searchCountry()"
                                @click.away="showCountryList = false"
                                autocomplete="off"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Type country..."
                            >
                            <!-- Country Dropdown -->
                            <div x-show="showCountryList && filteredCountries.length > 0" class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="c in filteredCountries" :key="c.name">
                                    <div 
                                        @click="selectCountry(c)"
                                        class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm flex items-center gap-3"
                                    >
                                        <img :src="c.flag" alt="" class="w-6 h-4 object-cover rounded-sm border border-gray-200">
                                        <span x-text="c.name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-1 relative">
                            <label for="address" class="block text-sm font-medium">
                                Street Address
                            </label>
                            <input
                                id="address"
                                name="address"
                                type="text"
                                x-model="address"
                                @input="searchAddress()"
                                @focus="searchAddress()"
                                @click.away="showAddressList = false"
                                autocomplete="off"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Start typing address..."
                            >
                            <!-- Address Dropdown -->
                            <div x-show="showAddressList && filteredAddresses.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="a in filteredAddresses" :key="a.display_name">
                                    <div 
                                        @click="selectAddress(a)"
                                        class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm border-b border-gray-100 dark:border-gray-600 last:border-0"
                                    >
                                        <div class="font-medium text-gray-800 dark:text-gray-200" x-text="a.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="a.display_name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1 relative">
                            <label for="city" class="block text-sm font-medium">
                                City
                            </label>
                            <input
                                id="city"
                                name="city"
                                type="text"
                                x-model="city"
                                @input="searchCity()"
                                @focus="searchCity()"
                                @click.away="showCityList = false"
                                autocomplete="off"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Type city..."
                                :disabled="!country"
                            >
                            <!-- City Dropdown -->
                            <div x-show="showCityList && filteredCities.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="c in filteredCities" :key="c.name">
                                    <div 
                                        @click="selectCity(c)"
                                        class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm"
                                        x-text="c.name"
                                    ></div>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-1 relative">
                            <label for="state" class="block text-sm font-medium" x-text="stateLabel">
                                State/Barangay
                            </label>
                            <input
                                id="state"
                                name="state"
                                type="text"
                                x-model="barangay"
                                @input="searchBarangay()"
                                @focus="searchBarangay()"
                                @click.away="showBarangayList = false"
                                autocomplete="off"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :placeholder="'Type ' + stateLabel.toLowerCase() + '...'"
                                :disabled="!city && isPhilippines"
                            >
                             <!-- Barangay Dropdown (Only for PH) -->
                             <div x-show="showBarangayList && filteredBarangays.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="b in filteredBarangays" :key="b">
                                    <div 
                                        @click="selectBarangay(b)"
                                        class="px-4 py-2 cursor-pointer hover:bg-indigo-100 dark:hover:bg-gray-600 text-sm"
                                        x-text="b"
                                    ></div>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="postal_code" class="block text-sm font-medium">
                                Postal code
                            </label>
                            <input
                                id="postal_code"
                                name="postal_code"
                                type="text"
                                x-model="postalCode"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="contact_person" class="block text-sm font-medium">
                                Contact person
                            </label>
                            <input
                                id="contact_person"
                                name="contact_person"
                                type="text"
                                value="{{ old('contact_person') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                        <div class="space-y-1">
                            <label for="contact_email" class="block text-sm font-medium">
                                Contact email
                            </label>
                            <input
                                id="contact_email"
                                name="contact_email"
                                type="email"
                                value="{{ old('contact_email') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="contact_phone" class="block text-sm font-medium">
                            Contact phone
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 sm:text-sm h-full" x-text="dialCode || '+--'">
                                </span>
                            </div>
                            <input
                                id="contact_phone"
                                name="contact_phone"
                                type="text"
                                x-model="phoneNumber"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-16"
                                placeholder="912 345 6789"
                            >
                            <!-- Hidden input to submit full phone number including code -->
                            <input type="hidden" name="contact_phone_full" :value="(dialCode ? dialCode + ' ' : '') + phoneNumber">
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-xs font-semibold uppercase tracking-wide text-white hover:bg-indigo-700"
                        >
                            Add company
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="font-semibold mb-3 text-lg">
                    Partner Company Directory
                </h3>
                @if ($companies->isEmpty())
                    <p class="text-sm text-gray-500 italic">
                        Walang nahanap na companies.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Industry</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Students</th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($companies as $company)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ showStudents: false }">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $company->industry ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $company->city ?? '-' }}, {{ $company->country ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @php
                                                // Handle potential null relationship gracefully
                                                $activeStudents = $company->assignments ? $company->assignments->where('status', 'active') : collect();
                                                $studentCount = $activeStudents->count();
                                            @endphp
                                            <button @click="showStudents = true" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors cursor-pointer">
                                                {{ $studentCount }} Students
                                            </button>

                                            <!-- Students Modal -->
                                            <div x-show="showStudents" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" @click="showStudents = false" aria-hidden="true" x-transition.opacity></div>

                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700" x-transition>
                                                        <div class="bg-gray-50 dark:bg-gray-900/80 px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                                Students Assigned to {{ $company->name }}
                                                            </h3>
                                                            <button @click="showStudents = false" class="text-gray-400 hover:text-gray-500">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="px-4 py-4 max-h-[60vh] overflow-y-auto">
                                                            @forelse($activeStudents as $assignment)
                                                                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                                            {{ substr($assignment->student->name, 0, 1) }}
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignment->student->name }}</div>
                                                                            <div class="text-xs text-gray-500">{{ $assignment->student->email }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <span class="text-xs text-gray-400">{{ $assignment->student->normalizedStudentSection() ?? \App\Models\User::STUDENT_SECTION_BSIT_4A }}</span>
                                                                </div>
                                                            @empty
                                                                <p class="text-sm text-gray-500 text-center py-4">No active students assigned.</p>
                                                            @endforelse
                                                        </div>
                                                        
                                                        <div class="bg-gray-50 dark:bg-gray-900/80 px-4 py-3 flex justify-end">
                                                            <button type="button" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700" @click="showStudents = false">
                                                                Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <div class="text-xs">{{ $company->contact_person ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $company->contact_email ?? '' }}</div>
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
    </script>
</x-coordinator-layout>

<x-admin-layout>
    <x-slot name="header">
        {{ __('Company Management') }}
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #locationSelectorMap { height: 300px; border-radius: 0.5rem; border: 1px solid #d1d5db; z-index: 1; }
        .dark #locationSelectorMap { border-color: #374151; }
        .wl-red-pin { width: 16px; height: 16px; border-radius: 50% 50% 50% 0; background: #ff0000; border: 2px solid #ffffff; transform: rotate(-45deg); box-shadow: 0 0 10px rgba(255,0,0,0.5); margin-top: -8px; margin-left: -8px; }
        .wl-blue-pin { width: 20px; height: 20px; border-radius: 50% 50% 50% 0; background: #3b82f6; border: 2px solid #ffffff; transform: rotate(-45deg); box-shadow: 0 0 10px rgba(59,130,246,0.45); margin-top: -10px; margin-left: -10px; }
    </style>

    <div class="py-12" x-data="{ 
        workOpportunities: [''],
        addOpportunity() { this.workOpportunities.push(''); },
        removeOpportunity(index) { this.workOpportunities.splice(index, 1); if (this.workOpportunities.length === 0) this.addOpportunity(); }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Success Message -->
            @if (session('status'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Admin Industry Map (same as coordinator map) -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Lapu-Lapu City Industry Map') }}</h2>
                    <div class="flex gap-2">
                        <button id="adminTogglePinMode" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-2 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pin a Flag</span>
                        </button>
                        <button id="adminCancelPinMode" style="display: none;" class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold rounded-lg transition-colors shadow-lg">Cancel</button>
                    </div>
                </div>
                <div id="adminLapuLapuMap" class="w-full rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700" data-companies='@json($companiesForMap)'></div>
                <p id="adminPinHelpText" style="display: none;" class="mt-2 text-xs text-rose-500 font-medium italic">* Click anywhere on the map to drop a red flag pin.</p>
            </div>

            <!-- Create Company Form -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Add New Company') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Create a new company record with its profile, location, and associated supervisor.') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('admin.companies.store') }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company Profile -->
                        <div class="space-y-4">
                            <h3 class="text-md font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Company Profile') }}</h3>
                            
                            <div>
                                <x-input-label for="name" :value="__('Company Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="industry" :value="__('Industry')" />
                                <x-text-input id="industry" name="industry" type="text" class="mt-1 block w-full" :value="old('industry')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('industry')" />
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Company Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Select Type') }}</option>
                                    @foreach(['Private', 'Government', 'NGO', 'Educational', 'Other'] as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>

                            <div>
                                <x-input-label for="default_supervisor_id" :value="__('Designated Supervisor')" />
                                <select id="default_supervisor_id" name="default_supervisor_id" class="mt-1 block w-full border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Select Supervisor') }}</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('default_supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }} ({{ $supervisor->email }})</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('default_supervisor_id')" />
                            </div>
                        </div>

                        <!-- Location & Contact -->
                        <div class="space-y-4">
                            <h3 class="text-md font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Location & Contact') }}</h3>
                            
                            <div class="mb-4">
                                <x-input-label :value="__('Pin Location on Map (Click to pin)')" class="mb-2" />
                                <div id="locationSelectorMap"></div>
                                <p class="mt-1 text-xs text-gray-500 italic">{{ __('Click anywhere on the map to set the company coordinates.') }}</p>
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Physical Address')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="relative">
                                    <x-input-label for="city" :value="__('City')" />
                                    <input id="city" name="city" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" list="admin-city-list" placeholder="Start typing city..." required />
                                    <datalist id="admin-city-list"></datalist>
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>
                                <div>
                                    <x-input-label for="state" :value="__('State/Province')" />
                                    <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('state')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="postal_code" :value="__('Postal Code')" />
                                    <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                                </div>
                                <div class="relative">
                                    <x-input-label for="country" :value="__('Country')" />
                                    <input id="country" name="country" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type country..." required />
                                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="contact_person" :value="__('Contact Person')" />
                                <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="latitude" :value="__('Latitude')" />
                                    <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full" :value="old('latitude')" placeholder="e.g. 10.3125" />
                                    <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                                </div>
                                <div>
                                    <x-input-label for="longitude" :value="__('Longitude')" />
                                    <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full" :value="old('longitude')" placeholder="e.g. 123.9458" />
                                    <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="contact_email" :value="__('Contact Email')" />
                                    <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" :value="old('contact_email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                                </div>
                                <div>
                                    <x-input-label for="contact_phone" :value="__('Contact Phone')" />
                                    <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" :value="old('contact_phone')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Opportunities -->
                    <div class="space-y-4">
                        <h3 class="text-md font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Available Work Opportunities') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ __('Specify the types of work students can perform at this company.') }}</p>
                        
                        <div class="space-y-2">
                            <template x-for="(opportunity, index) in workOpportunities" :key="index">
                                <div class="flex items-center gap-2">
                                    <x-text-input name="work_opportunities[]" type="text" class="block w-full" x-model="workOpportunities[index]" placeholder="e.g., Software Development, Data Analysis" required />
                                    <button type="button" @click="removeOpportunity(index)" class="p-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addOpportunity()" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('+ Add Another Work Type') }}
                            </button>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('work_opportunities')" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Save Company') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Companies List -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Existing Companies') }}
                    </h2>
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Company') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type/Industry') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Supervisor') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Work Opportunities') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($companies as $company)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $company->city }}, {{ $company->country }}</div>
                                        @if($company->latitude && $company->longitude)
                                            <div class="text-[10px] text-indigo-500 font-mono">{{ $company->latitude }}, {{ $company->longitude }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $company->type }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">{{ $company->industry }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $company->defaultSupervisor->name ?? __('Not Assigned') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @if($company->work_opportunities)
                                                @foreach($company->work_opportunities as $opp)
                                                    <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded">{{ $opp }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @php $confirmMsg = __('Are you sure you want to delete this company record?'); @endphp
                                        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" onsubmit="return confirm('{{ $confirmMsg }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ __('No company records found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @push('scripts')
                <script>
                (function(){
                    // Location Selector Map Logic
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');
                    const mapEl = document.getElementById('locationSelectorMap');
                    
                    if (mapEl) {
                        const defaultCenter = [10.3098, 123.9448]; // LLCC Area
                        const map = L.map('locationSelectorMap').setView(defaultCenter, 13);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(map);

                        let marker;
                        const redIcon = L.divIcon({
                            className: '',
                            html: '<div class="wl-red-pin"></div>',
                            iconSize: [16, 16],
                            iconAnchor: [8, 8],
                        });

                        map.on('click', function(e) {
                            const { lat, lng } = e.latlng;
                            
                            if (marker) {
                                marker.setLatLng(e.latlng);
                            } else {
                                marker = L.marker(e.latlng, { icon: redIcon }).addTo(map);
                            }
                            
                            latInput.value = lat.toFixed(8);
                            lngInput.value = lng.toFixed(8);
                        });

                        // If there are old values, show marker
                        if (latInput.value && lngInput.value) {
                            const pos = [parseFloat(latInput.value), parseFloat(lngInput.value)];
                            marker = L.marker(pos, { icon: redIcon }).addTo(map);
                            map.setView(pos, 15);
                        }
                    }

                    const countryEl = document.getElementById('country');
                    const cityEl = document.getElementById('city');
                    const datalist = document.getElementById('admin-city-list');
                    let cities = [];

                    async function loadCitiesForCountry(countryName){
                        if(!countryName) return;
                        try{
                            const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities',{ 
                                method:'POST',
                                headers:{'Content-Type':'application/json'},
                                body: JSON.stringify({ country: countryName })
                            });
                            const data = await res.json();
                            cities = Array.isArray(data.data) ? data.data : [];
                            renderDatalist('');
                        }catch(e){
                            cities = [];
                            datalist.innerHTML = '';
                        }
                    }

                    function renderDatalist(query){
                        const q = (query||'').toLowerCase();
                        const items = cities
                          .filter(c => !q || c.toLowerCase().startsWith(q))
                          .slice(0, 50);
                        datalist.innerHTML = items.map(c => '<option value=\"'+c.replace(/\"/g,'&quot;')+'\"></option>').join('');
                    }

                    countryEl && countryEl.addEventListener('input', e => {
                        loadCitiesForCountry(e.target.value.trim());
                    });
                    cityEl && cityEl.addEventListener('input', e => {
                        if(cities.length === 0 && countryEl && countryEl.value.trim().length){
                            loadCitiesForCountry(countryEl.value.trim()).then(()=>renderDatalist(e.target.value));
                        } else {
                            renderDatalist(e.target.value);
                        }
                    });

                    // Admin Lapu-Lapu City Industry Map (same behavior as coordinator)
                    const adminMapEl = document.getElementById('adminLapuLapuMap');
                    if (adminMapEl) {
                        adminMapEl.style.height = '450px';
                        const companiesMap = JSON.parse(adminMapEl.dataset.companies || '[]');
                        const savedPinsMap = JSON.parse(adminMapEl.dataset.pins || '[]');
                        const llccCoords = [10.3098, 123.9448];

                        let pinModeAdmin = false;
                        const toggleBtnA = document.getElementById('adminTogglePinMode');
                        const cancelBtnA = document.getElementById('adminCancelPinMode');
                        const helpTextA = document.getElementById('adminPinHelpText');

                        const mapAdmin = L.map('adminLapuLapuMap', { zoomControl: true, scrollWheelZoom: false }).setView(llccCoords, 14);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(mapAdmin);

                        toggleBtnA.addEventListener('click', () => {
                            pinModeAdmin = !pinModeAdmin;
                            if (pinModeAdmin) {
                                adminMapEl.style.cursor = 'crosshair';
                                toggleBtnA.classList.replace('bg-rose-600', 'bg-emerald-600');
                                toggleBtnA.querySelector('span').innerText = 'Click Map to Pin';
                                cancelBtnA.style.display = 'block';
                                helpTextA.style.display = 'block';
                            } else {
                                resetAdminPinMode();
                            }
                        });

                        cancelBtnA.addEventListener('click', resetAdminPinMode);

                        function resetAdminPinMode() {
                            pinModeAdmin = false;
                            adminMapEl.style.cursor = '';
                            toggleBtnA.classList.replace('bg-emerald-600', 'bg-rose-600');
                            toggleBtnA.querySelector('span').innerText = 'Pin a Flag';
                            cancelBtnA.style.display = 'none';
                            helpTextA.style.display = 'none';
                        }

                        mapAdmin.on('click', async function(e) {
                            if (!pinModeAdmin) return;
                            const label = prompt('Enter a label for this flag (optional):');
                            if (label === null) return;

                            try {
                                const response = await fetch('{{ route("map-pins.store") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        label: label || 'Custom Flag',
                                        latitude: e.latlng.lat,
                                        longitude: e.latlng.lng,
                                        type: 'flag',
                                        color: '#ef4444'
                                    })
                                });

                                if (response.ok) {
                                    const pin = await response.json();
                                    addAdminSavedPinToMap(pin);
                                    resetAdminPinMode();
                                }
                            } catch (err) {
                                alert('Failed to save pin. Please try again.');
                            }
                        });

                        function addAdminSavedPinToMap(pin) {
                            const icon = L.divIcon({ className: '', html: `<div class="wl-red-pin" style="background: ${pin.color}"></div>`, iconSize: [20, 20], iconAnchor: [10, 10] });
                            const popupContent = `
                                <div class="p-1">
                                    <div class="font-black text-rose-600">${pin.label}</div>
                                    <div class="text-[10px] text-gray-400 mt-1 italic">Manual Pin</div>
                                    <button onclick="deleteAdminPin(${pin.id}, this)" class="mt-2 text-[10px] text-white bg-red-500 px-2 py-0.5 rounded hover:bg-red-600 transition-colors">Delete Pin</button>
                                </div>
                            `;

                            L.marker([pin.latitude, pin.longitude], { icon }).addTo(mapAdmin).bindPopup(popupContent);
                        }

                        window.deleteAdminPin = async function(id) {
                            if (!confirm('Are you sure you want to delete this pin?')) return;
                            try {
                                const response = await fetch(`/map-pins/${id}`, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                if (response.ok) {
                                    location.reload();
                                }
                            } catch (err) {
                                alert('Delete failed.');
                            }
                        };

                        savedPinsMap.forEach(addAdminSavedPinToMap);

                        const llccIcon = L.divIcon({ className: '', html: '<div class="wl-blue-pin"></div>', iconSize: [24, 24], iconAnchor: [12, 12] });
                        L.marker(llccCoords, { icon: llccIcon }).addTo(mapAdmin).bindPopup('<div class="font-bold text-indigo-600 text-center">Lapu-Lapu City College</div><div class="text-[10px] text-gray-500 text-center">OJT Landmark</div>').openPopup();

                        const redIcon = L.divIcon({ className: '', html: '<div class="wl-red-pin"></div>', iconSize: [20, 20], iconAnchor: [10, 10] });

                        const geocode = async (query) => {
                            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=3&viewbox=123.8,10.4,124.1,10.2&bounded=1&q=' + encodeURIComponent(query);
                            const res = await fetch(url, { headers: { Accept: 'application/json' } });
                            const data = await res.json();
                            if (Array.isArray(data) && data.length) {
                                return { lat: Number(data[0].lat), lng: Number(data[0].lon) };
                            }
                            return null;
                        };

                        (async () => {
                            for (const c of companiesMap) {
                                let pos = null;

                                if (c.latitude && c.longitude) {
                                    pos = { lat: Number(c.latitude), lng: Number(c.longitude) };
                                } else {
                                    const queries = [`${c.name}, Lapu-Lapu City`, `${c.address}, Lapu-Lapu City`, `${c.name}, Cebu`];
                                    for (const q of queries) {
                                        try {
                                            pos = await geocode(q);
                                            if (pos) break;
                                        } catch (err) {}
                                    }
                                }

                                if (pos && Number.isFinite(pos.lat) && Number.isFinite(pos.lng)) {
                                    const companyCoords = [pos.lat, pos.lng];
                                    const label = `
                                        <div class="p-1">
                                            <div class="font-black text-rose-600">${c.name}</div>
                                            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">${c.industry || 'Industry'}</div>
                                            <div class="text-[10px] text-gray-400 mt-1 italic truncate max-w-[150px]">${c.address || ''}</div>
                                        </div>
                                    `;

                                    L.marker(companyCoords, { icon: redIcon }).addTo(mapAdmin).bindPopup(label);
                                    L.polyline([llccCoords, companyCoords], {
                                        color: '#6366f1', weight: 2, opacity: 0.5, dashArray: '5, 10', className: 'map-connection-line'
                                    }).addTo(mapAdmin);
                                }
                            }
                        })();
                    }
                })();
                </script>
                @endpush
            </div>
        </div>
    </div>
</x-admin-layout>

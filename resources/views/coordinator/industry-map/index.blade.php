@extends('layouts.app')

@section('title', 'Industry Map - Company Location Tracker')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-indigo-950 to-black p-6">
    <div class="max-w-full mx-auto">
        <!-- MAP ON TOP - FULL WIDTH -->
        <div class="mb-6 mt-8">
            <h1 class="text-3xl font-bold text-white mb-4">📍 Industry Map</h1>
            <div class="bg-white/10 backdrop-blur rounded-xl shadow-2xl overflow-hidden border border-white/20" style="height: 400px;">
                <div id="map" style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
            </div>
        </div>

        <!-- CONTROLS BELOW MAP -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Instructions -->
            <div class="bg-white/10 backdrop-blur rounded-xl shadow-lg p-5 border border-white/20">
                <h3 class="text-base font-bold text-white mb-3">💡 How to Pin</h3>
                <ol class="space-y-2">
                    <li class="flex gap-2 text-sm text-gray-300">
                        <span class="text-pink-400 font-bold">1.</span>
                        <span>Click "Activate Pin Mode"</span>
                    </li>
                    <li class="flex gap-2 text-sm text-gray-300">
                        <span class="text-pink-400 font-bold">2.</span>
                        <span>Click on map</span>
                    </li>
                    <li class="flex gap-2 text-sm text-gray-300">
                        <span class="text-pink-400 font-bold">3.</span>
                        <span>Enter company name</span>
                    </li>
                </ol>
            </div>

            <!-- Controls -->
            <div class="bg-white/10 backdrop-blur rounded-xl shadow-lg p-5 border border-white/20 space-y-4">
                <!-- Action Buttons -->
                <div>
                    <button id="pinModeBtn" onclick="togglePinMode()" class="w-full bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 shadow-lg text-sm">
                        ▶ Activate Pin Mode
                    </button>
                    <button id="cancelModeBtn" onclick="togglePinMode()" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg transition hidden text-sm shadow-lg">
                        ✕ Cancel Mode
                    </button>
                </div>

                <!-- Status Message -->
                <div id="statusDiv" class="hidden bg-blue-500/20 border border-blue-400/50 rounded-lg p-3 text-xs text-blue-200 text-center">
                    📍 Click on map to place pin
                </div>
            </div>

            <!-- Pinned Companies -->
            <div class="lg:col-span-2 bg-white/10 backdrop-blur rounded-xl shadow-lg p-5 border border-white/20">
                <h3 class="text-base font-bold text-white mb-3">📍 Pinned Companies</h3>
                <div id="pinnedCompaniesDiv" class="space-y-2 max-h-64 overflow-y-auto pr-2">
                    <p class="text-xs text-gray-500 text-center py-4">No pinned companies yet</p>
                </div>
            </div>
        </div>

        <!-- BOTTOM: Company Details Section (Full Width) -->
        <div id="detailsSection" class="hidden mt-8 p-8 bg-white/10 backdrop-blur rounded-xl shadow-2xl border border-white/20">
            <!-- Company Header -->
            <div class="flex justify-between items-start mb-8 pb-6 border-b border-white/20">
                <div class="flex-1">
                    <h2 id="companyDetailName" class="text-3xl font-bold text-white mb-4"></h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Industry</p>
                            <p id="companyDetailIndustry" class="text-white font-semibold mt-2"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Address</p>
                            <p id="companyDetailAddress" class="text-white font-semibold mt-2"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Coordinates</p>
                            <p id="companyDetailCoords" class="text-white font-mono text-sm mt-2"></p>
                        </div>
                    </div>
                </div>
                <button id="deleteBtn" onclick="deletePin()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg text-sm font-bold transition shadow-lg whitespace-nowrap ml-4">
                    🗑️ Delete Pin
                </button>
            </div>

            <!-- Students Section -->
            <div>
                <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <span class="mr-3">👥</span> Assigned Students
                </h3>

                <!-- No Students State -->
                <div id="noStudentsDiv" class="text-center py-16 bg-white/5 rounded-lg border-2 border-dashed border-white/20">
                    <p class="text-gray-400 text-lg">No students assigned to this company</p>
                </div>

                <!-- Students Grid -->
                <div id="studentsGridDiv" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Student cards will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pin Location Modal - Enter Company Name -->
<div id="selectCompanyModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-xl p-8 w-full max-w-md shadow-2xl border border-indigo-500/40">
        <h2 class="text-2xl font-bold text-white mb-2">Enter Company Name</h2>
        <p class="text-gray-400 text-sm mb-6">Name the company at this location</p>

        <div id="modalError" class="hidden bg-red-500/20 border border-red-500/40 text-red-200 px-4 py-3 rounded-lg mb-4 text-sm"></div>

        <!-- Company Name Input -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-300 mb-2">Company Name:</label>
            <input type="text" id="modalCompanyName" placeholder="Enter company name..." class="w-full border border-indigo-500/40 rounded-lg px-4 py-2 bg-gray-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
        </div>

        <!-- Modal Action Buttons -->
        <div class="flex gap-3">
            <button onclick="closeModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 rounded-lg transition">
                Cancel
            </button>
            <button onclick="confirmModalPin()" class="flex-1 text-white font-bold py-2 rounded-lg transition shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                Place Pin
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <script>
        // Global Variables
        let map = null;
        let isPinMode = false;
        let currentMarkers = {};
        let companies = [];
        let selectedCompany = null;
        let pendingPinLat = null;
        let pendingPinLng = null;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initMap, 100);
        });

        // Initialize Leaflet map
        function initMap() {
            try {
                if (typeof L === 'undefined') {
                    console.error('Leaflet not loaded');
                    setTimeout(initMap, 100);
                    return;
                }

                const mapElement = document.getElementById('map');
                if (!mapElement) {
                    console.error('Map element not found');
                    return;
                }

                // Create map centered on Lapu-Lapu City
                map = L.map('map').setView([10.3206, 123.9724], 14);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19,
                    minZoom: 10
                }).addTo(map);

                // Load pins and companies
                loadPins();

                // Handle map clicks in pin mode
                map.on('click', (e) => {
                    if (isPinMode) {
                        pendingPinLat = e.latlng.lat;
                        pendingPinLng = e.latlng.lng;
                        openModal();
                    }
                });

                console.log('Map initialized successfully');
            } catch (error) {
                console.error('Map initialization error:', error);
            }
        }

        // Load all pins from server
        function loadPins() {
            try {
                const pins = {!! json_encode($mapPins ?? []) !!};

                // Remove old markers
                Object.values(currentMarkers).forEach(item => {
                    if (map && item.marker) map.removeLayer(item.marker);
                });
                currentMarkers = {};

                // Add new markers
                pins.forEach(pin => {
                    if (map) {
                        const marker = L.marker([pin.latitude, pin.longitude], {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            })
                        }).addTo(map)
                            .bindPopup(`<div class="p-2"><strong class="text-sm">${pin.label}</strong></div>`)
                            .on('click', () => showCompanyDetails(pin.company_id));

                        currentMarkers[pin.company_id] = {
                            marker: marker,
                            label: pin.label
                        };
                    }
                });

                updatePinnedList();
            } catch (error) {
                console.error('Error loading pins:', error);
            }
        }

        // Load unpinned companies for dropdown
        function loadCompanies() {
            // No longer needed - removed dropdown
        }

        // Update company dropdowns - removed
        function updateCompanyDropdown() {
            // Removed
        }

        // Toggle pin mode on/off
        function togglePinMode() {
            isPinMode = !isPinMode;

            document.getElementById('pinModeBtn').classList.toggle('hidden');
            document.getElementById('cancelModeBtn').classList.toggle('hidden');
            document.getElementById('statusDiv').classList.toggle('hidden');

            if (isPinMode) {
                document.getElementById('map').style.cursor = 'crosshair';
            } else {
                document.getElementById('map').style.cursor = 'grab';
                closeModal();
            }
        }

        // Open company selection modal
        function openModal() {
            const modal = document.getElementById('selectCompanyModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.getElementById('modalError').classList.add('hidden');
            }
        }

        // Close company selection modal
        function closeModal() {
            const modal = document.getElementById('selectCompanyModal');
            if (modal) {
                modal.classList.add('hidden');
            }
            document.getElementById('modalCompanyName').value = '';
            document.getElementById('modalError').classList.add('hidden');
            pendingPinLat = null;
            pendingPinLng = null;
        }

        // Confirm pin from modal
        function confirmModalPin() {
            const companyName = document.getElementById('modalCompanyName').value.trim();
            const errorDiv = document.getElementById('modalError');

            if (!companyName) {
                errorDiv.textContent = 'Please enter a company name';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (!pendingPinLat || !pendingPinLng) {
                errorDiv.textContent = 'Invalid location selected';
                errorDiv.classList.remove('hidden');
                return;
            }

            // Send pin to server
            fetch('/coordinator/industry-map/pin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    company_name: companyName,
                    latitude: pendingPinLat,
                    longitude: pendingPinLng
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    loadPins();
                    togglePinMode();
                } else {
                    errorDiv.textContent = data.message || 'Error saving pin';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                errorDiv.textContent = 'Error saving pin';
                errorDiv.classList.remove('hidden');
            });
        }

        // Alternative save function
        function savePinAndCompany() {
            confirmModalPin();
        }

        // Show company details below
        function showCompanyDetails(companyId) {
            fetch(`/coordinator/industry-map/company/${companyId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    selectedCompany = data;

                    // Update company info
                    document.getElementById('companyDetailName').textContent = data.name || 'Unknown';
                    document.getElementById('companyDetailIndustry').textContent = data.industry || 'N/A';
                    document.getElementById('companyDetailAddress').textContent = data.address || 'N/A';
                    document.getElementById('companyDetailCoords').textContent = `${data.latitude || 'N/A'}, ${data.longitude || 'N/A'}`;

                    // Show details section
                    const detailsSection = document.getElementById('detailsSection');
                    detailsSection.classList.remove('hidden');
                    
                    // Scroll to details
                    setTimeout(() => {
                        detailsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);

                    // Render students
                    if (!data.students || data.students.length === 0) {
                        document.getElementById('noStudentsDiv').classList.remove('hidden');
                        document.getElementById('studentsGridDiv').classList.add('hidden');
                    } else {
                        document.getElementById('noStudentsDiv').classList.add('hidden');
                        document.getElementById('studentsGridDiv').classList.remove('hidden');

                        document.getElementById('studentsGridDiv').innerHTML = data.students.map(student => `
                            <div class="bg-white/10 backdrop-blur rounded-lg p-4 border border-white/20 hover:border-pink-400/50 transition">
                                <div class="mb-3">
                                    <p class="font-bold text-white text-sm">${student.name}</p>
                                    <p class="text-xs text-gray-500">${student.student_number}</p>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">
                                    <span class="font-semibold">${student.program || 'N/A'}</span> • Year ${student.year_level || 'N/A'}
                                </p>
                                <p class="text-xs text-gray-400 mb-3">${student.department || 'N/A'} - ${student.section || 'N/A'}</p>
                                <div class="flex gap-2 flex-wrap">
                                    <span class="text-xs px-2 py-1 rounded-full font-semibold ${student.work_status === 'Active' ? 'bg-green-500/20 text-green-200' : 'bg-gray-500/20 text-gray-300'}">
                                        ${student.work_status || 'N/A'}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full font-semibold bg-blue-500/20 text-blue-200">
                                        ${student.approved_hours || 0}/${student.required_hours || 0} hrs
                                    </span>
                                </div>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => console.error('Error fetching company details:', error));
        }

        // Update sidebar pinned companies list
        function updatePinnedList() {
            const pinnedDiv = document.getElementById('pinnedCompaniesDiv');
            if (Object.keys(currentMarkers).length === 0) {
                pinnedDiv.innerHTML = '<p class="text-xs text-gray-500 text-center py-4">No pinned companies</p>';
            } else {
                pinnedDiv.innerHTML = Object.entries(currentMarkers).map(([companyId, item]) => {
                    return `
                        <button onclick="showCompanyDetails(${companyId})" class="w-full text-left bg-white/10 hover:bg-white/20 backdrop-blur rounded-lg p-3 border border-white/10 hover:border-pink-400/50 transition">
                            <p class="font-semibold text-white text-sm">${item.label || 'Unknown'}</p>
                            <p class="text-xs text-gray-400 mt-1">📍 View details</p>
                        </button>
                    `;
                }).join('');
            }
        }

        // Delete pin
        function deletePin() {
            if (!selectedCompany) return;

            if (!confirm(`Delete pin for ${selectedCompany.name}?`)) return;

            fetch(`/coordinator/industry-map/pin/${selectedCompany.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadPins();
                    loadCompanies();
                    document.getElementById('detailsSection').classList.add('hidden');
                }
            })
            .catch(error => console.error('Delete error:', error));
        }
    </script>
@endpush

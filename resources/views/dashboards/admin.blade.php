<x-admin-layout>
    <x-slot name="header">
        Admin dashboard
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-indigo-600/20 backdrop-blur-md border border-indigo-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(79,70,229,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-indigo-500/20 hover:bg-indigo-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-indigo-200 font-bold uppercase tracking-wider group-hover:text-indigo-100 transition-colors">
                        Total users
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-indigo-50 transition-colors">
                        {{ $totalUsers }}
                    </div>
                </div>
            </div>
            <div class="bg-emerald-600/20 backdrop-blur-md border border-emerald-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(5,150,105,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-emerald-500/20 hover:bg-emerald-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-emerald-200 font-bold uppercase tracking-wider group-hover:text-emerald-100 transition-colors">
                        Supervisors
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-emerald-50 transition-colors">
                        {{ $supervisors }}
                    </div>
                </div>
            </div>
            <div class="bg-cyan-600/20 backdrop-blur-md border border-cyan-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(8,145,178,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-cyan-500/20 hover:bg-cyan-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-cyan-200 font-bold uppercase tracking-wider group-hover:text-cyan-100 transition-colors">
                        Coordinators
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-cyan-50 transition-colors">
                        {{ $coordinators }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-5">
            <div class="bg-blue-600/20 backdrop-blur-md border border-blue-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(37,99,235,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-blue-500/20 hover:bg-blue-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-blue-200 font-bold uppercase tracking-wider group-hover:text-blue-100 transition-colors">
                        OJT Students
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-blue-50 transition-colors">
                        {{ $students }}
                    </div>
                </div>
            </div>
            <div class="bg-purple-600/20 backdrop-blur-md border border-purple-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(147,51,234,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-purple-500/20 hover:bg-purple-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-purple-200 font-bold uppercase tracking-wider group-hover:text-purple-100 transition-colors">
                        Work logs
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-purple-50 transition-colors">
                        {{ $workLogs }}
                    </div>
                </div>
            </div>
            <div class="bg-amber-600/20 backdrop-blur-md border border-amber-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(217,119,6,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-amber-500/20 hover:bg-amber-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-amber-200 font-bold uppercase tracking-wider group-hover:text-amber-100 transition-colors">
                        Companies
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-amber-50 transition-colors">
                        {{ $companies }}
                    </div>
                </div>
            </div>
            <div class="bg-rose-600/20 backdrop-blur-md border border-rose-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(225,29,72,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-rose-500/20 hover:bg-rose-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-rose-200 font-bold uppercase tracking-wider group-hover:text-rose-100 transition-colors">
                        Assignments
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-rose-50 transition-colors">
                        {{ $assignments }}
                    </div>
                </div>
            </div>
            <div class="bg-red-600/20 backdrop-blur-md border border-red-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(220,38,38,0.1)] overflow-hidden hover:scale-105 transition-all duration-300 hover:shadow-red-500/20 hover:bg-red-600/30 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-red-200 font-bold uppercase tracking-wider group-hover:text-red-100 transition-colors">
                        Pending reviews
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-red-50 transition-colors">
                        {{ $pendingReviews }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- User Distribution Doughnut Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.001 0 0120.488 9z" />
                    </svg>
                    User Role Distribution
                </h3>
                <div class="relative h-[300px]">
                    <canvas id="userDistributionChart" 
                        data-labels="{{ json_encode(array_keys($userDistribution)) }}"
                        data-values="{{ json_encode(array_values($userDistribution)) }}">
                    </canvas>
                </div>
            </div>

            <!-- Registration Trends Line Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    New Account Trends
                </h3>
                <div class="relative h-[300px]">
                    <canvas id="registrationTrendsChart"
                        data-labels="{{ json_encode($registrationTrends->pluck('month')) }}"
                        data-values="{{ json_encode($registrationTrends->pluck('count')) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <div class="glass-panel overflow-hidden">
            <div class="p-6 text-gray-100 space-y-3">
                <h3 class="font-bold text-white">
                    Management
                </h3>
                <div class="flex flex-wrap gap-3">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-500 shadow-lg shadow-indigo-900/50 transition-all hover:scale-105"
                        >
                            Manage users
                        </a>
                        <a
                            href="{{ route('admin.companies.index') }}"
                            class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-bold hover:bg-blue-500 shadow-lg shadow-blue-900/50 transition-all hover:scale-105"
                        >
                            Manage companies
                        </a>
                        <a
                            href="{{ route('admin.users.index') }}#create-user"
                        class="inline-flex items-center px-3 py-2 rounded-md bg-purple-600 text-white text-sm font-bold hover:bg-purple-500 shadow-lg shadow-purple-900/50 transition-all hover:scale-105"
                    >
                        Create user
                    </a>
                </div>
            </div>
        </div>

        <div class="glass-panel overflow-hidden">
            <div class="p-6 text-gray-100 space-y-3">
                <h3 class="font-bold text-white">
                    Recent users
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="px-3 py-2 text-white font-semibold uppercase tracking-wider">Name</th>
                                <th class="px-3 py-2 text-white font-semibold uppercase tracking-wider">Email</th>
                                <th class="px-3 py-2 text-white font-semibold uppercase tracking-wider">Role</th>
                                <th class="px-3 py-2 text-white font-semibold uppercase tracking-wider">Joined</th>
                                <th class="px-3 py-2 text-white font-semibold uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentUsers as $u)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap font-medium text-white">{{ $u->name }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-100">{{ $u->email }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <x-user-role-badge :role="$u->role" />
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-100">{{ $u->created_at->diffForHumans() }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-100">
                                        @if (!$u->is_approved)
                                            <div class="flex gap-2">
                                                <form action="{{ route('admin.users.approve', $u) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-emerald-600 hover:text-white transition-all duration-200">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.users.reject', $u) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request? This will delete the account.');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600/20 text-rose-400 border border-rose-500/30 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-rose-600 hover:text-white transition-all duration-200">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 text-gray-100 border border-white/10 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-red-600/20 hover:text-red-400 hover:border-red-500/30 transition-all duration-200">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-sm text-gray-100 font-semibold">
                                        No users yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Distribution Chart
            const distCanvas = document.getElementById('userDistributionChart');
            const userDistributionLabels = JSON.parse(distCanvas.dataset.labels);
            const userDistributionData = JSON.parse(distCanvas.dataset.values);

            const distCtx = distCanvas.getContext('2d');
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: userDistributionLabels,
                    datasets: [{
                        data: userDistributionData,
                        backgroundColor: [
                            '#4f46e5', // indigo
                            '#0891b2', // cyan
                            '#059669', // emerald
                            '#2563eb', // blue
                            '#7c3aed', // purple
                        ],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#9ca3af',
                                padding: 20,
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // Registration Trends Chart
            const trendCanvas = document.getElementById('registrationTrendsChart');
            const registrationTrendsLabels = JSON.parse(trendCanvas.dataset.labels);
            const registrationTrendsData = JSON.parse(trendCanvas.dataset.values);

            const trendCtx = trendCanvas.getContext('2d');
            const gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: registrationTrendsLabels,
                    datasets: [{
                        label: 'New Users',
                        data: registrationTrendsData,
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>

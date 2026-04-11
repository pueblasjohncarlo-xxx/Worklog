<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Admin Dashboard</h2>
                <p class="text-gray-400 text-sm mt-1">System overview and management controls</p>
            </div>
            <div class="text-sm text-gray-400">
                Last updated: {{ now()->format('M d, Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- ===== SUMMARY CARDS SECTION ===== -->
        <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Key Metrics</h3>
            
            <!-- Row 1: Core User Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                <!-- Total Users -->
                <div class="group bg-gradient-to-br from-indigo-600/20 to-indigo-600/10 border border-indigo-500/30 rounded-lg p-4 hover:border-indigo-500/50 transition-all hover:shadow-lg hover:shadow-indigo-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-indigo-200 font-semibold uppercase tracking-wider">Total Users</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $totalUsers }}</p>
                            <p class="text-xs text-indigo-300 mt-1">{{ $totalApprovedUsers }} approved</p>
                        </div>
                        <svg class="h-8 w-8 text-indigo-400/40 group-hover:text-indigo-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="group bg-gradient-to-br from-green-600/20 to-green-600/10 border border-green-500/30 rounded-lg p-4 hover:border-green-500/50 transition-all hover:shadow-lg hover:shadow-green-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-green-200 font-semibold uppercase tracking-wider">Active Users</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $activeUsers }}</p>
                            <p class="text-xs text-green-300 mt-1">Last 7 days</p>
                        </div>
                        <svg class="h-8 w-8 text-green-400/40 group-hover:text-green-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                    </div>
                </div>

                <!-- Total Companies -->
                <div class="group bg-gradient-to-br from-cyan-600/20 to-cyan-600/10 border border-cyan-500/30 rounded-lg p-4 hover:border-cyan-500/50 transition-all hover:shadow-lg hover:shadow-cyan-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-cyan-200 font-semibold uppercase tracking-wider">Companies</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $companies }}</p>
                            <p class="text-xs text-cyan-300 mt-1">Partner organizations</p>
                        </div>
                        <svg class="h-8 w-8 text-cyan-400/40 group-hover:text-cyan-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm4 8H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm10 12h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-8V3h8v2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Row 2: Role Distribution -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Admins -->
                <div class="group bg-gradient-to-br from-purple-600/20 to-purple-600/10 border border-purple-500/30 rounded-lg p-3 hover:border-purple-500/50 transition-all hover:shadow-lg hover:shadow-purple-500/20">
                    <p class="text-xs text-purple-200 font-semibold uppercase tracking-wider">Admins</p>
                    <p class="text-xl font-black text-white mt-1">{{ $admins }}</p>
                </div>

                <!-- Coordinators -->
                <div class="group bg-gradient-to-br from-blue-600/20 to-blue-600/10 border border-blue-500/30 rounded-lg p-3 hover:border-blue-500/50 transition-all hover:shadow-lg hover:shadow-blue-500/20">
                    <p class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Coordinators</p>
                    <p class="text-xl font-black text-white mt-1">{{ $coordinators }}</p>
                </div>

                <!-- Supervisors -->
                <div class="group bg-gradient-to-br from-emerald-600/20 to-emerald-600/10 border border-emerald-500/30 rounded-lg p-3 hover:border-emerald-500/50 transition-all hover:shadow-lg hover:shadow-emerald-500/20">
                    <p class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Supervisors</p>
                    <p class="text-xl font-black text-white mt-1">{{ $supervisors }}</p>
                </div>

                <!-- OJT Advisers -->
                <div class="group bg-gradient-to-br from-rose-600/20 to-rose-600/10 border border-rose-500/30 rounded-lg p-3 hover:border-rose-500/50 transition-all hover:shadow-lg hover:shadow-rose-500/20">
                    <p class="text-xs text-rose-200 font-semibold uppercase tracking-wider">OJT Advisers</p>
                    <p class="text-xl font-black text-white mt-1">{{ $advisers }}</p>
                </div>

                <!-- Students -->
                <div class="group bg-gradient-to-br from-indigo-600/20 to-indigo-600/10 border border-indigo-500/30 rounded-lg p-3 hover:border-indigo-500/50 transition-all hover:shadow-lg hover:shadow-indigo-500/20">
                    <p class="text-xs text-indigo-200 font-semibold uppercase tracking-wider">Students</p>
                    <p class="text-xl font-black text-white mt-1">{{ $students }}</p>
                </div>
            </div>
        </div>

        <!-- ===== WORK LOG METRICS ===== -->
        <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Work Logs & Assignments</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Total Work Logs -->
                <div class="group bg-gradient-to-br from-purple-600/20 to-purple-600/10 border border-purple-500/30 rounded-lg p-4 hover:border-purple-500/50 transition-all hover:shadow-lg hover:shadow-purple-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-purple-200 font-semibold uppercase tracking-wider">Total Work Logs</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $workLogs }}</p>
                        </div>
                        <svg class="h-8 w-8 text-purple-400/40 group-hover:text-purple-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7h-3v3h3v-3zm-3-2h3V6h-3v4zm3 5h3v-3h-3v3zm2-5h3V6h-3v4z" />
                        </svg>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="group bg-gradient-to-br from-red-600/20 to-red-600/10 border border-red-500/30 rounded-lg p-4 hover:border-red-500/50 transition-all hover:shadow-lg hover:shadow-red-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-red-200 font-semibold uppercase tracking-wider">Pending Reviews</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $pendingReviews }}</p>
                        </div>
                        <svg class="h-8 w-8 text-red-400/40 group-hover:text-red-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                    </div>
                </div>

                <!-- Approved Work Logs -->
                <div class="group bg-gradient-to-br from-emerald-600/20 to-emerald-600/10 border border-emerald-500/30 rounded-lg p-4 hover:border-emerald-500/50 transition-all hover:shadow-lg hover:shadow-emerald-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Approved</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $approvedWorkLogs }}</p>
                        </div>
                        <svg class="h-8 w-8 text-emerald-400/40 group-hover:text-emerald-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                    </div>
                </div>

                <!-- Total Assignments -->
                <div class="group bg-gradient-to-br from-amber-600/20 to-amber-600/10 border border-amber-500/30 rounded-lg p-4 hover:border-amber-500/50 transition-all hover:shadow-lg hover:shadow-amber-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-200 font-semibold uppercase tracking-wider">Assignments</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $assignments }}</p>
                        </div>
                        <svg class="h-8 w-8 text-amber-400/40 group-hover:text-amber-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ANNOUNCEMENTS & MISC ===== -->
        <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Content Management</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Total Announcements -->
                <div class="group bg-gradient-to-br from-cyan-600/20 to-cyan-600/10 border border-cyan-500/30 rounded-lg p-4 hover:border-cyan-500/50 transition-all hover:shadow-lg hover:shadow-cyan-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-cyan-200 font-semibold uppercase tracking-wider">Announcements</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $announcements }}</p>
                            <p class="text-xs text-cyan-300 mt-1">Posted to all users</p>
                        </div>
                        <svg class="h-8 w-8 text-cyan-400/40 group-hover:text-cyan-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13.5h8v-2H3v2zm0 4h8v-2H3v2zm0-8h8v-2H3v2zm15 0.5h2V9h3V7.5h-3v-3H16v3h-3V9h3v4.5z" />
                        </svg>
                    </div>
                </div>

                <!-- Audit Logs Count -->
                <div class="group bg-gradient-to-br from-indigo-600/20 to-indigo-600/10 border border-indigo-500/30 rounded-lg p-4 hover:border-indigo-500/50 transition-all hover:shadow-lg hover:shadow-indigo-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-indigo-200 font-semibold uppercase tracking-wider">Audit Logs</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $recentAuditLogs->count() }}+</p>
                            <p class="text-xs text-indigo-300 mt-1">Recent activities logged</p>
                        </div>
                        <svg class="h-8 w-8 text-indigo-400/40 group-hover:text-indigo-400/60 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.12-2.86-1.41 1.41L10.5 19l4.96-6.71z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CHARTS SECTION ===== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- User Role Distribution Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    <h3 class="text-lg font-bold text-white">User Role Distribution</h3>
                </div>
                <div class="relative h-[300px]">
                    <canvas id="userDistributionChart" 
                        data-labels="{{ json_encode(array_keys($userDistribution)) }}"
                        data-values="{{ json_encode(array_values($userDistribution)) }}">
                    </canvas>
                </div>
            </div>

            <!-- User Approval Status Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-emerald-500/20 rounded-xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                    </svg>
                    <h3 class="text-lg font-bold text-white">User Approval Status</h3>
                </div>
                <div class="relative h-[300px]">
                    <canvas id="userApprovalChart" 
                        data-labels="{{ json_encode(array_keys($userApprovalStatus)) }}"
                        data-values="{{ json_encode(array_values($userApprovalStatus)) }}">
                    </canvas>
                </div>
            </div>

            <!-- Registration Trends Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-cyan-500/20 rounded-xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7h-3v3h3v-3zm-3-2h3V6h-3v4zm3 5h3v-3h-3v3z" />
                    </svg>
                    <h3 class="text-lg font-bold text-white">New Accounts Trend</h3>
                </div>
                <div class="relative h-[250px]">
                    <canvas id="registrationTrendsChart"
                        data-labels="{{ json_encode($registrationTrends->pluck('month')) }}"
                        data-values="{{ json_encode($registrationTrends->pluck('count')) }}">
                    </canvas>
                </div>
            </div>

            <!-- Work Log Trends Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-purple-500/20 rounded-xl p-6 shadow-xl">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13.5h8v-2H3v2zm0 4h8v-2H3v2zm0-8h8v-2H3v2zm15 0.5h2V9h3V7.5h-3v-3H16v3h-3V9h3v4.5z" />
                    </svg>
                    <h3 class="text-lg font-bold text-white">Work Logs Submitted</h3>
                </div>
                <div class="relative h-[250px]">
                    <canvas id="workLogTrendsChart"
                        data-labels="{{ json_encode($workLogTrends->pluck('month')) }}"
                        data-values="{{ json_encode($workLogTrends->pluck('count')) }}">
                    </canvas>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#9ca3af',
                            padding: 15,
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                }
            };

            // User Distribution Chart
            const distCanvas = document.getElementById('userDistributionChart');
            if (distCanvas && distCanvas.dataset.labels) {
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
                                '#7c3aed', // purple
                                '#0891b2', // cyan
                                '#059669', // emerald
                                '#2563eb', // blue
                                '#f43f5e', // rose
                            ],
                            borderWidth: 0,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        cutout: '70%'
                    }
                });
            }

            // User Approval Status Chart
            const approvalCanvas = document.getElementById('userApprovalChart');
            if (approvalCanvas && approvalCanvas.dataset.labels) {
                const approvalLabels = JSON.parse(approvalCanvas.dataset.labels);
                const approvalData = JSON.parse(approvalCanvas.dataset.values);
                const approvalCtx = approvalCanvas.getContext('2d');
                
                new Chart(approvalCtx, {
                    type: 'pie',
                    data: {
                        labels: approvalLabels,
                        datasets: [{
                            data: approvalData,
                            backgroundColor: [
                                '#10b981', // emerald
                                '#f59e0b', // amber
                            ],
                            borderWidth: 0,
                            hoverOffset: 12
                        }]
                    },
                    options: {
                        ...chartDefaults
                    }
                });
            }

            // Registration Trends Chart
            const trendCanvas = document.getElementById('registrationTrendsChart');
            if (trendCanvas && trendCanvas.dataset.labels) {
                const registrationTrendsLabels = JSON.parse(trendCanvas.dataset.labels);
                const registrationTrendsData = JSON.parse(trendCanvas.dataset.values);
                const trendCtx = trendCanvas.getContext('2d');
                const gradient = trendCtx.createLinearGradient(0, 0, 0, 250);
                gradient.addColorStop(0, 'rgba(6, 182, 212, 0.3)');
                gradient.addColorStop(1, 'rgba(6, 182, 212, 0)');

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: registrationTrendsLabels,
                        datasets: [{
                            label: 'New Users',
                            data: registrationTrendsData,
                            borderColor: '#06b6d4',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#06b6d4',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        plugins: {
                            ...chartDefaults.plugins,
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
            }

            // Work Log Trends Chart
            const workLogCanvas = document.getElementById('workLogTrendsChart');
            if (workLogCanvas && workLogCanvas.dataset.labels) {
                const workLogLabels = JSON.parse(workLogCanvas.dataset.labels);
                const workLogData = JSON.parse(workLogCanvas.dataset.values);
                const workLogCtx = workLogCanvas.getContext('2d');
                const workLogGradient = workLogCtx.createLinearGradient(0, 0, 0, 250);
                workLogGradient.addColorStop(0, 'rgba(168, 85, 247, 0.3)');
                workLogGradient.addColorStop(1, 'rgba(168, 85, 247, 0)');

                new Chart(workLogCtx, {
                    type: 'line',
                    data: {
                        labels: workLogLabels,
                        datasets: [{
                            label: 'Work Logs',
                            data: workLogData,
                            borderColor: '#a855f7',
                            backgroundColor: workLogGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#a855f7',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        plugins: {
                            ...chartDefaults.plugins,
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
            }
        });
    </script>
</x-admin-layout>

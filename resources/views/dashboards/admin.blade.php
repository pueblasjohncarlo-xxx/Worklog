<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Admin Dashboard</h2>
                <p class="text-gray-400 text-[10px] mt-0">System overview</p>
            </div>
            <div class="text-[10px] text-gray-500">
                Updated: {{ now()->format('H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 pb-4">
        <!-- ===== ROW 1: CORE METRICS & QUICKS STATS (NOW TOP) ===== -->
        <div class="flex flex-col xl:flex-row gap-4">
            <!-- Summary Grid -->
            <div class="xl:w-3/4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Users -->
                <div class="group bg-gradient-to-br from-indigo-600/20 to-indigo-600/10 border border-indigo-500/30 rounded-lg p-3 hover:border-indigo-500/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Total Users</p>
                            <p class="text-xl font-black text-white mt-0.5">{{ $totalUsers }}</p>
                            <p class="text-[10px] text-indigo-300">{{ $totalApprovedUsers }} appr.</p>
                        </div>
                        <svg class="h-6 w-6 text-indigo-400/40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" /></svg>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="group bg-gradient-to-br from-green-600/20 to-green-600/10 border border-green-500/30 rounded-lg p-3 hover:border-green-500/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-green-200 font-bold uppercase tracking-wider">Active</p>
                            <p class="text-xl font-black text-white mt-0.5">{{ $activeUsers }}</p>
                            <p class="text-[10px] text-green-300">Last 7 days</p>
                        </div>
                        <svg class="h-6 w-6 text-green-400/40" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" /></svg>
                    </div>
                </div>

                <!-- Companies -->
                <div class="group bg-gradient-to-br from-cyan-600/20 to-cyan-600/10 border border-cyan-500/30 rounded-lg p-3 hover:border-cyan-500/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-cyan-200 font-bold uppercase tracking-wider">Companies</p>
                            <p class="text-xl font-black text-white mt-0.5">{{ $companies }}</p>
                            <p class="text-[10px] text-cyan-300">Partners</p>
                        </div>
                        <svg class="h-6 w-6 text-cyan-400/40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm4 8H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm10 12h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-8V3h8v2z" /></svg>
                    </div>
                </div>

                <!-- Pending -->
                <div class="group bg-gradient-to-br from-red-600/20 to-red-600/10 border border-red-500/30 rounded-lg p-3 hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-red-200 font-bold uppercase tracking-wider">Pending</p>
                            <p class="text-xl font-black text-white mt-0.5">{{ $pendingReviews }}</p>
                            <p class="text-[10px] text-red-300">To Review</p>
                        </div>
                        <svg class="h-6 w-6 text-red-400/40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" /></svg>
                    </div>
                </div>
            </div>

            <!-- Mini Fast-Access Stats (Side Info) -->
            <div class="flex-1 grid grid-cols-4 xl:grid-cols-2 gap-2">
                <div class="bg-black/20 border border-white/5 rounded p-1.5 text-center">
                    <p class="text-[9px] text-gray-500 font-bold uppercase italic">Log Subm.</p>
                    <p class="text-sm font-black text-white leading-none mt-1">{{ $workLogs }}</p>
                </div>
                <div class="bg-black/20 border border-white/5 rounded p-1.5 text-center">
                    <p class="text-[9px] text-gray-500 font-bold uppercase italic">Assigned</p>
                    <p class="text-sm font-black text-white leading-none mt-1">{{ $assignments }}</p>
                </div>
                <div class="bg-black/20 border border-white/5 rounded p-1.5 text-center">
                    <p class="text-[9px] text-gray-500 font-bold uppercase italic">Students</p>
                    <p class="text-sm font-black text-white leading-none mt-1">{{ $students }}</p>
                </div>
                <div class="bg-black/20 border border-white/5 rounded p-1.5 text-center">
                    <p class="text-[9px] text-red-500 font-bold uppercase italic">Audit</p>
                    <p class="text-sm font-black text-white leading-none mt-1">{{ $recentAuditLogs->count() }}</p>
                </div>
            </div>
        </div>

        <!-- ===== ROW 2: MAIN VISUALS (UPPER HALF) ===== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Role Distribution -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-3 shadow-lg">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase mb-2">Role Mix</h3>
                <div class="relative h-[160px]">
                    <canvas id="userDistributionChart" 
                        data-labels="{{ json_encode(array_keys($userDistribution)) }}"
                        data-values="{{ json_encode(array_values($userDistribution)) }}"></canvas>
                </div>
            </div>

            <!-- Approval Status -->
            <div class="bg-black/40 backdrop-blur-md border border-emerald-500/20 rounded-lg p-3 shadow-lg">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase mb-2">Approval Status</h3>
                <div class="relative h-[160px]">
                    <canvas id="userApprovalChart" 
                        data-labels="{{ json_encode(array_keys($userApprovalStatus)) }}"
                        data-values="{{ json_encode(array_values($userApprovalStatus)) }}"></canvas>
                </div>
            </div>

            <!-- Account Trends -->
            <div class="bg-black/40 backdrop-blur-md border border-cyan-500/20 rounded-lg p-3 shadow-lg">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase mb-2">New Accounts</h3>
                <div class="relative h-[160px]">
                    <canvas id="registrationTrendsChart"
                        data-labels="{{ json_encode($registrationTrends->pluck('month')) }}"
                        data-values="{{ json_encode($registrationTrends->pluck('count')) }}"></canvas>
                </div>
            </div>

            <!-- Work Log Trends -->
            <div class="bg-black/40 backdrop-blur-md border border-purple-500/20 rounded-lg p-3 shadow-lg">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase mb-2">Activities</h3>
                <div class="relative h-[160px]">
                    <canvas id="workLogTrendsChart"
                        data-labels="{{ json_encode($workLogTrends->pluck('month')) }}"
                        data-values="{{ json_encode($workLogTrends->pluck('count')) }}"></canvas>
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

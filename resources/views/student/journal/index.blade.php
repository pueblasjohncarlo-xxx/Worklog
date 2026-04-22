<x-student-layout>
    <x-slot name="header">
        Accomplishment Reports
    </x-slot>

    <div class="space-y-6">
        <!-- New Report Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="bg-indigo-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-2 text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-bold tracking-wide">Submit New Accomplishment Report</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('student.worklogs.create', ['type' => 'daily']) }}" class="flex flex-col items-center p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-all group">
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-3 group-hover:scale-110 transition-transform">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 19v-7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-widest">Daily Report</p>
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1">Summary of your day</p>
                    </a>
                    <a href="{{ route('student.worklogs.create', ['type' => 'weekly']) }}" class="flex flex-col items-center p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all group">
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-3 group-hover:scale-110 transition-transform">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="text-sm font-black text-emerald-700 dark:text-emerald-300 uppercase tracking-widest">Weekly Report</p>
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1">Review of the week</p>
                    </a>
                    <a href="{{ route('student.worklogs.create', ['type' => 'monthly']) }}" class="flex flex-col items-center p-6 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all group">
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-3 group-hover:scale-110 transition-transform">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 19v-7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-black text-amber-700 dark:text-amber-300 uppercase tracking-widest">Monthly Report</p>
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1">Monthly performance</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="bg-slate-900 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-2 text-white">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 19v-7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="text-lg font-bold tracking-wide">Daily Report Calendar</h3>
                </div>
                
                <div class="flex items-center gap-4 text-white">
                    <a href="{{ route('student.journal.index', ['date' => $prevMonth->toDateString()]) }}" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <span class="text-xl font-black uppercase tracking-widest">{{ $currentDate->format('F Y') }}</span>
                    <a href="{{ route('student.journal.index', ['date' => $nextMonth->toDateString()]) }}" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-7 mb-2">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="text-center text-xs font-black uppercase tracking-widest text-slate-600 py-2">{{ $day }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 gap-px bg-gray-100 dark:bg-gray-700 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-inner">
                    @foreach($calendar as $day)
                        <div class="min-h-[120px] bg-white dark:bg-gray-800 p-2 group relative {{ !$day['is_current_month'] ? 'opacity-40 grayscale-[0.5]' : '' }}">
                            @if($day['can_write'])
                                <a href="{{ route('student.worklogs.create', ['type' => 'daily', 'date' => $day['date']->toDateString()]) }}"
                                   class="absolute inset-0 z-10"
                                              aria-label="Write journal for {{ $day['date']->format('M d, Y') }}">
                                </a>
                            @endif
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-black {{ $day['date']->isToday() ? 'bg-indigo-600 text-white h-6 w-6 flex items-center justify-center rounded-full shadow-sm' : 'text-gray-700 dark:text-gray-200' }}">
                                    {{ $day['date']->day }}
                                </span>
                                
                                @if($day['log'])
                                    @if($day['log']->status === 'approved')
                                        <svg class="h-4 w-4 text-emerald-600 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($day['log']->status === 'submitted')
                                        <svg class="h-4 w-4 text-amber-500 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                @else
                                    @if($day['is_current_month'] && $day['date']->isPast())
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-600 text-[11px] font-black leading-none text-white shadow-sm">X</span>
                                    @endif
                                @endif
                            </div>

                            <div class="flex flex-col gap-1">
                                @if($day['log'] && $day['log']->type === 'daily' && $day['log']->description)
                                    <button 
                                        onclick="openJournalModal(this)"
                                        data-id="{{ $day['log']->id }}"
                                        data-type="{{ $day['log']->type }}"
                                        data-date="{{ $day['date']->format('M d, Y') }}"
                                        data-content="{{ $day['log']->description }}"
                                        data-skills="{{ $day['log']->skills_applied }}"
                                        data-reflection="{{ $day['log']->reflection }}"
                                        data-comment="{{ $day['log']->reviewer_comment }}"
                                        class="w-full py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded shadow-sm flex items-center justify-center gap-1 transition-colors"
                                    >
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Journal
                                    </button>
                                @elseif($day['can_write'])
                                    <a href="{{ route('student.worklogs.create', ['type' => 'daily', 'date' => $day['date']->toDateString()]) }}" class="w-full py-2 border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-xs font-black rounded-xl flex items-center justify-center transition-all">
                                        Write
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-wrap gap-6 text-xs font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-emerald-200"></span>
                        <span class="text-slate-700 font-semibold">Approved</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-amber-500 ring-2 ring-amber-200"></span>
                        <span class="text-slate-700 font-semibold">Pending Approval</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-600 text-[11px] font-black leading-none text-white shadow-sm">X</span>
                        <span class="text-slate-700 font-semibold">Absent/Rejected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded bg-indigo-600 ring-2 ring-indigo-200"></span>
                        <span class="text-slate-700 font-semibold">Journal Entry</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-50 dark:border-gray-700 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="font-bold text-gray-900 dark:text-gray-100">New Journal Entry</h4>
                </div>
                <div class="p-6 text-center">
                    <div class="grid grid-cols-3 gap-4">
                        <a href="{{ route('student.worklogs.create', ['type' => 'daily']) }}" class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800 hover:bg-indigo-100 transition-colors">
                            <p class="text-xs font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-widest">Daily</p>
                        </a>
                        <a href="{{ route('student.worklogs.create', ['type' => 'weekly']) }}" class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800 hover:bg-emerald-100 transition-colors">
                            <p class="text-xs font-black text-emerald-700 dark:text-emerald-300 uppercase tracking-widest">Weekly</p>
                        </a>
                        <a href="{{ route('student.worklogs.create', ['type' => 'monthly']) }}" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800 hover:bg-amber-100 transition-colors">
                            <p class="text-xs font-black text-amber-700 dark:text-amber-300 uppercase tracking-widest">Monthly</p>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-50 dark:border-gray-700 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h4 class="font-bold text-gray-900 dark:text-gray-100">Report History</h4>
                </div>
                <div class="p-6">
                    @php
                        $recentReports = collect();
                        if (isset($assignment->id)) {
                            $recentReports = \App\Models\WorkLog::where('assignment_id', $assignment->id)
                                ->whereNotNull('description')
                                ->whereIn('type', ['daily', 'weekly', 'monthly'])
                                ->latest('work_date')
                                ->get()
                                ->groupBy('type');
                        }
                    @endphp

                    <div x-data="{ tab: 'daily' }" class="space-y-4">
                        <div class="flex border-b border-gray-100 dark:border-gray-700">
                            <button @click="tab = 'daily'" :class="tab === 'daily' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 text-xs font-black uppercase tracking-widest border-b-2 transition-all">Daily</button>
                            <button @click="tab = 'weekly'" :class="tab === 'weekly' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 text-xs font-black uppercase tracking-widest border-b-2 transition-all">Weekly</button>
                            <button @click="tab = 'monthly'" :class="tab === 'monthly' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 text-xs font-black uppercase tracking-widest border-b-2 transition-all">Monthly</button>
                        </div>

                        <template x-if="tab === 'daily'">
                            <div class="space-y-4">
                                @forelse($recentReports->get('daily', []) as $report)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">{{ $report->work_date->format('M d, Y') }}</span>
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                                {{ $report->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                {{ $report->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $report->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $report->status === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}
                                            ">
                                                {{ $report->status }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2 italic">"{{ $report->description }}"</p>
                                        <div class="mt-2 flex justify-between items-center">
                                            <span class="text-[10px] font-bold text-indigo-600">{{ $report->hours }} Hours</span>
                                            <div class="flex gap-2">
                                                <button 
                                                    data-id="{{ $report->id }}"
                                                    data-type="{{ $report->type }}" 
                                                    data-date="{{ $report->work_date->format('M d, Y') }}" 
                                                    data-content="{{ $report->description }}" 
                                                    data-skills="{{ $report->skills_applied }}"
                                                    data-reflection="{{ $report->reflection }}"
                                                    data-comment="{{ $report->reviewer_comment }}"
                                                    onclick="openJournalModal(this)" 
                                                    class="text-[10px] font-bold text-indigo-700 hover:text-indigo-900 uppercase tracking-widest"
                                                >
                                                    View Details
                                                </button>
                                                <a href="{{ route('student.worklogs.print', $report->id) }}" class="text-[10px] font-bold text-slate-700 hover:text-slate-900 uppercase tracking-widest">Print</a>
                                                @if($report->status === 'draft' || $report->status === 'rejected')
                                                    <a href="{{ route('student.worklogs.edit', $report->id) }}" class="text-[10px] font-bold text-emerald-700 hover:text-emerald-900 uppercase tracking-widest">Edit</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-700 italic text-center py-8 dark:text-slate-300">No daily reports found.</p>
                                @endforelse
                            </div>
                        </template>

                        <template x-if="tab === 'weekly'">
                            <div class="space-y-4">
                                @forelse($recentReports->get('weekly', []) as $report)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">{{ $report->work_date->format('M d, Y') }}</span>
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                                {{ $report->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                {{ $report->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $report->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $report->status === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}
                                            ">
                                                {{ $report->status }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 italic">"{{ $report->description }}"</p>
                                            <div class="mt-2 flex justify-end gap-2">
                                                <button 
                                                    data-id="{{ $report->id }}"
                                                    data-type="{{ $report->type }}" 
                                                    data-date="{{ $report->work_date->format('M d, Y') }}" 
                                                    data-content="{{ $report->description }}" 
                                                    data-skills="{{ $report->skills_applied }}"
                                                    data-reflection="{{ $report->reflection }}"
                                                    data-comment="{{ $report->reviewer_comment }}"
                                                    onclick="openJournalModal(this)" 
                                                    class="text-[10px] font-bold text-indigo-600 hover:text-indigo-900 uppercase tracking-widest"
                                                >
                                                    View Details
                                                </button>
                                                <a href="{{ route('student.worklogs.print', $report->id) }}" class="text-[10px] font-bold text-slate-600 hover:text-slate-900 uppercase tracking-widest">Print</a>
                                                @if($report->status === 'draft' || $report->status === 'rejected')
                                                    <a href="{{ route('student.worklogs.edit', $report->id) }}" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-900 uppercase tracking-widest">Edit</a>
                                                @endif
                                            </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-700 italic text-center py-8 dark:text-slate-300">No weekly reports found.</p>
                                @endforelse
                            </div>
                        </template>

                        <template x-if="tab === 'monthly'">
                            <div class="space-y-4">
                                @forelse($recentReports->get('monthly', []) as $report)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">{{ $report->work_date->format('M d, Y') }}</span>
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest
                                                {{ $report->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                {{ $report->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $report->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $report->status === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}
                                            ">
                                                {{ $report->status }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 italic">"{{ $report->description }}"</p>
                                            <div class="mt-2 flex justify-end gap-2">
                                                <button 
                                                    data-id="{{ $report->id }}"
                                                    data-type="{{ $report->type }}" 
                                                    data-date="{{ $report->work_date->format('M d, Y') }}" 
                                                    data-content="{{ $report->description }}" 
                                                    data-skills="{{ $report->skills_applied }}"
                                                    data-reflection="{{ $report->reflection }}"
                                                    data-comment="{{ $report->reviewer_comment }}"
                                                    onclick="openJournalModal(this)" 
                                                    class="text-[10px] font-bold text-indigo-600 hover:text-indigo-900 uppercase tracking-widest"
                                                >
                                                    View Details
                                                </button>
                                                <a href="{{ route('student.worklogs.print', $report->id) }}" class="text-[10px] font-bold text-slate-600 hover:text-slate-900 uppercase tracking-widest">Print</a>
                                                @if($report->status === 'draft' || $report->status === 'rejected')
                                                    <a href="{{ route('student.worklogs.edit', $report->id) }}" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-900 uppercase tracking-widest">Edit</a>
                                                @endif
                                            </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-700 italic text-center py-8 dark:text-slate-300">No monthly reports found.</p>
                                @endforelse
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Journal View Modal -->
                <div id="journalModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-slate-900/75" aria-hidden="true" onclick="closeJournalModal()"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white uppercase tracking-widest" id="modalTitle">Report Details</h3>
                                <button onclick="closeJournalModal()" class="text-white hover:text-indigo-200 transition-colors">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-4">
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Report Type</p>
                                        <p class="text-sm font-bold text-indigo-600" id="modalType"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</p>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100" id="modalDate"></p>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Task/Activity Description</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap italic leading-relaxed" id="modalContent"></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Skills Applied/Learned</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap italic leading-relaxed" id="modalSkills"></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Remarks/Reflection</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap italic leading-relaxed" id="modalReflection"></p>
                                </div>

                                <div id="modalFeedbackSection" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800 hidden">
                                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Supervisor Feedback</p>
                                    <p class="text-sm text-amber-800 dark:text-amber-200 italic font-medium" id="modalComment"></p>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex justify-end gap-3">
                                <a id="modalPrintBtn" href="#" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-indigo-700 transition-colors">Print Report</a>
                                <button onclick="closeJournalModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-gray-300 transition-colors">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function openJournalModal(button) {
                        const id = button.getAttribute('data-id');
                        const type = button.getAttribute('data-type');
                        const date = button.getAttribute('data-date');
                        const content = button.getAttribute('data-content');
                        const skills = button.getAttribute('data-skills') || 'No skills listed.';
                        const reflection = button.getAttribute('data-reflection') || 'No reflection provided.';
                        const comment = button.getAttribute('data-comment');
                        
                        document.getElementById('modalType').innerText = type.toUpperCase();
                        document.getElementById('modalDate').innerText = date;
                        document.getElementById('modalContent').innerText = content;
                        document.getElementById('modalSkills').innerText = skills;
                        document.getElementById('modalReflection').innerText = reflection;
                        
                        // Set print button href
                        const printBtn = document.getElementById('modalPrintBtn');
                        printBtn.href = `/student/worklogs/${id}/print`;
                        
                        const feedbackSection = document.getElementById('modalFeedbackSection');
                        if (comment && comment !== 'null') {
                            document.getElementById('modalComment').innerText = comment;
                            feedbackSection.classList.remove('hidden');
                        } else {
                            feedbackSection.classList.add('hidden');
                        }
                        
                        document.getElementById('journalModal').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }

                    function closeJournalModal() {
                        document.getElementById('journalModal').classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }
                </script>
            </div>
        </div>
    </div>
</x-student-layout>

<x-student-layout>
    <x-slot name="header">
        Edit Worklog Entry
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Warning Alert -->
        @if($workLog->status !== 'draft')
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-xl">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700 font-bold uppercase tracking-wider">
                            Pahinumdom: Ang pag-edit niini mobalik sa "Draft" status ug kinahanglan nimo i-submit og balik para ma-approve.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-slate-900 px-8 py-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200 dark:shadow-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-widest">Update Entry</h3>
                        <p class="text-indigo-300 text-xs font-bold uppercase tracking-tighter mt-0.5">Ref: LOG-{{ str_pad($workLog->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border-2
                        {{ $workLog->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : '' }}
                        {{ $workLog->status === 'submitted' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}
                        {{ $workLog->status === 'draft' ? 'bg-gray-50 text-gray-600 border-gray-200' : '' }}
                        {{ $workLog->status === 'rejected' ? 'bg-rose-50 text-rose-600 border-rose-200' : '' }}
                    ">
                        Status: {{ $workLog->status }}
                    </span>
                </div>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('student.worklogs.update', $workLog->id) }}" class="space-y-8" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @php $isTemplateBased = $workLog->time_in === null && $workLog->time_out === null && in_array($workLog->type, ['daily', 'weekly', 'monthly'], true); @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Date Input -->
                        <div class="space-y-2">
                            <label for="work_date" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 19v-7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Log Date
                            </label>
                            <input
                                id="work_date"
                                name="work_date"
                                type="date"
                                value="{{ old('work_date', $workLog->work_date->format('Y-m-d')) }}"
                                required
                                class="block w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all py-4 px-6 font-bold"
                            >
                            @error('work_date') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Hours Input (Calculated) -->
                        <div class="space-y-2">
                            <label for="hours" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Total Hours {{ $isTemplateBased ? '(Editable)' : '(Auto)' }}
                            </label>
                            <div class="relative">
                                <input
                                    id="hours"
                                    name="hours"
                                    type="number"
                                    step="0.25"
                                    min="0"
                                    max="24"
                                    value="{{ old('hours', $workLog->hours) }}"
                                    @if(! $isTemplateBased) readonly @endif
                                    class="block w-full rounded-2xl border-gray-200 dark:border-gray-700 shadow-sm transition-all py-4 px-6 font-black {{ $isTemplateBased ? 'bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500' : 'bg-indigo-50/50 dark:bg-indigo-900/10 text-indigo-600 dark:text-indigo-400' }}"
                                >
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <span class="text-xs font-black text-indigo-400 uppercase">HRS</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($workLog->time_in !== null || $workLog->time_out !== null)
                    <!-- Time In / Time Out Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <div class="space-y-2">
                            <label for="time_in" class="flex items-center gap-2 text-xs font-black text-emerald-600 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Time In
                            </label>
                            <input
                                id="time_in"
                                name="time_in"
                                type="time"
                                value="{{ old('time_in', $workLog->time_in ? \Carbon\Carbon::parse($workLog->time_in)->format('H:i') : '') }}"
                                class="time-input block w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all py-4 px-6 font-bold"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="time_out" class="flex items-center gap-2 text-xs font-black text-rose-600 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Time Out
                            </label>
                            <input
                                id="time_out"
                                name="time_out"
                                type="time"
                                value="{{ old('time_out', $workLog->time_out ? \Carbon\Carbon::parse($workLog->time_out)->format('H:i') : '') }}"
                                class="time-input block w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all py-4 px-6 font-bold"
                            >
                        </div>
                    </div>
                    @endif

                    @if($isTemplateBased)
                        <div class="rounded-3xl border border-indigo-200/60 dark:border-indigo-800/50 bg-indigo-50/70 dark:bg-indigo-900/15 p-6 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-extrabold uppercase tracking-widest text-indigo-700 dark:text-indigo-300">Template-based submission</p>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-1">
                                        Download the official template, fill it out in Word/Google Docs, then upload the completed file as your accomplishment report.
                                    </p>
                                </div>
                                <a
                                    id="downloadTemplateBtn"
                                    href="{{ route('student.accomplishment-reports.template', ['type' => $workLog->type, 'work_date' => old('work_date', $workLog->work_date->format('Y-m-d')), 'hours' => old('hours', $workLog->hours)]) }}"
                                    class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-extrabold uppercase tracking-widest hover:bg-indigo-700 transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                    </svg>
                                    Download Template
                                </a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="rounded-lg bg-white/80 dark:bg-black/20 border border-white/60 dark:border-white/10 p-3">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Report type</div>
                                    <div class="text-sm font-extrabold text-gray-800 dark:text-gray-100">{{ strtoupper($workLog->type) }}</div>
                                </div>
                                <div class="rounded-lg bg-white/80 dark:bg-black/20 border border-white/60 dark:border-white/10 p-3 md:col-span-2">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Covered period</div>
                                    <div id="coveredPeriod" class="text-sm font-extrabold text-gray-800 dark:text-gray-100"></div>
                                    <div class="text-[11px] text-gray-600 dark:text-gray-300 mt-1">Weekly = Monday–Friday. Monthly = entire month.</div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="attachment" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Upload Completed Report (DOC/DOCX/ODT/PDF)
                            </label>

                            @if($workLog->attachment_path)
                                <div class="flex items-center justify-between gap-3 mb-2 p-3 bg-indigo-50 rounded-2xl border border-indigo-100">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-xs text-indigo-800 font-bold">Current file: {{ basename($workLog->attachment_path) }}</span>
                                    </div>
                                    <a href="{{ route('student.worklogs.attachment', $workLog->id) }}?inline=1" target="_blank" class="text-xs font-black uppercase tracking-widest text-indigo-700 hover:underline">
                                        View
                                    </a>
                                </div>
                            @endif

                            <input
                                id="attachment"
                                name="attachment"
                                type="file"
                                accept=".doc,.docx,.odt,.pdf"
                                @if(! $workLog->attachment_path) required @endif
                                class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:tracking-wider file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 dark:file:bg-indigo-900 dark:file:text-indigo-200 transition-all cursor-pointer bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-2"
                            >
                            @error('attachment') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Accepted: DOC, DOCX, ODT, PDF (max 10MB).</p>
                        </div>

                        <script>
                            (function () {
                                const type = @json($workLog->type);
                                const dateEl = document.getElementById('work_date');
                                const hoursEl = document.getElementById('hours');
                                const coveredEl = document.getElementById('coveredPeriod');
                                const downloadBtn = document.getElementById('downloadTemplateBtn');
                                const baseUrl = @json(route('student.accomplishment-reports.template'));

                                function parseYmd(s) {
                                    const m = /^\d{4}-\d{2}-\d{2}$/.test(s) ? s : null;
                                    if (!m) return null;
                                    const [y, mo, d] = s.split('-').map(Number);
                                    return new Date(Date.UTC(y, mo - 1, d));
                                }

                                function fmt(d) {
                                    if (!d) return '';
                                    const opts = { year: 'numeric', month: 'short', day: '2-digit', timeZone: 'UTC' };
                                    return new Intl.DateTimeFormat('en-US', opts).format(d);
                                }

                                function startOfWeekMon(date) {
                                    const day = date.getUTCDay();
                                    const diff = (day === 0 ? -6 : 1 - day);
                                    const res = new Date(date);
                                    res.setUTCDate(res.getUTCDate() + diff);
                                    return res;
                                }

                                function endOfWeekFri(date) {
                                    const mon = startOfWeekMon(date);
                                    const fri = new Date(mon);
                                    fri.setUTCDate(mon.getUTCDate() + 4);
                                    return fri;
                                }

                                function startOfMonth(date) {
                                    return new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), 1));
                                }

                                function endOfMonth(date) {
                                    return new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + 1, 0));
                                }

                                function update() {
                                    const ymd = dateEl?.value || '';
                                    const d = parseYmd(ymd);

                                    let label = '';
                                    if (!d) {
                                        label = '—';
                                    } else if (type === 'daily') {
                                        label = fmt(d);
                                    } else if (type === 'weekly') {
                                        label = fmt(startOfWeekMon(d)) + ' - ' + fmt(endOfWeekFri(d));
                                    } else {
                                        const start = startOfMonth(d);
                                        const end = endOfMonth(d);
                                        label = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', timeZone: 'UTC' }).format(d) +
                                            ' (' + fmt(start) + ' - ' + fmt(end) + ')';
                                    }

                                    if (coveredEl) coveredEl.textContent = label;

                                    const params = new URLSearchParams();
                                    params.set('type', type);
                                    if (ymd) params.set('work_date', ymd);
                                    if (type === 'daily' && hoursEl?.value) params.set('hours', hoursEl.value);

                                    if (downloadBtn) downloadBtn.href = baseUrl + '?' + params.toString();
                                }

                                if (dateEl) dateEl.addEventListener('change', update);
                                if (hoursEl) hoursEl.addEventListener('input', update);
                                update();
                            })();
                        </script>
                    @else
                        <!-- Description Textarea -->
                        <div class="space-y-2">
                            <label for="description" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Task/Activity Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                required
                                placeholder="Describe the tasks and activities you completed..."
                                class="block w-full rounded-3xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all p-6 font-medium italic leading-relaxed"
                            >{{ old('description', $workLog->description) }}</textarea>
                            @error('description') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Skills Applied Textarea -->
                        <div class="space-y-2">
                            <label for="skills_applied" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Skills Applied/Learned
                            </label>
                            <textarea
                                id="skills_applied"
                                name="skills_applied"
                                rows="3"
                                placeholder="What technical or soft skills did you use or learn?"
                                class="block w-full rounded-3xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all p-6 font-medium italic leading-relaxed"
                            >{{ old('skills_applied', $workLog->skills_applied) }}</textarea>
                            @error('skills_applied') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Reflection Textarea -->
                        <div class="space-y-2">
                            <label for="reflection" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Remarks/Reflection
                            </label>
                            <textarea
                                id="reflection"
                                name="reflection"
                                rows="3"
                                placeholder="Reflect on your experience during this period..."
                                class="block w-full rounded-3xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all p-6 font-medium italic leading-relaxed"
                            >{{ old('reflection', $workLog->reflection) }}</textarea>
                            @error('reflection') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Attachment -->
                        <div class="space-y-2">
                            <label for="attachment" class="flex items-center gap-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Attachment (Doc, PPT, PDF)
                            </label>
                            @if($workLog->attachment_path)
                                <div class="flex items-center gap-2 mb-2 p-2 bg-indigo-50 rounded-lg border border-indigo-100">
                                    <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-xs text-indigo-700 font-medium">Current file: {{ basename($workLog->attachment_path) }}</span>
                                </div>
                            @endif
                            <input
                                id="attachment"
                                name="attachment"
                                type="file"
                                accept=".doc,.docx,.ppt,.pptx,.pdf"
                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:tracking-wider file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-400 transition-all cursor-pointer bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-2"
                            >
                            @error('attachment') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between gap-4 pt-6 border-t border-gray-50 dark:border-gray-700">
                        <a
                            href="{{ route('student.dashboard') }}"
                            class="flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 uppercase tracking-widest transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Cancel Changes
                        </a>
                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center px-10 py-4 rounded-2xl bg-indigo-600 text-sm font-black uppercase tracking-widest text-white hover:bg-indigo-700 shadow-xl shadow-indigo-200 dark:shadow-none transition-all hover:-translate-y-0.5"
                            >
                                Save Changes
                            </button>
                            <button
                                type="button"
                                onclick="submitWorkLog()"
                                class="inline-flex items-center px-10 py-4 rounded-2xl bg-emerald-600 text-sm font-black uppercase tracking-widest text-white hover:bg-emerald-700 shadow-xl shadow-emerald-200 dark:shadow-none transition-all hover:-translate-y-0.5"
                            >
                                Save & Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeInInput = document.getElementById('time_in');
            const timeOutInput = document.getElementById('time_out');
            const hoursInput = document.getElementById('hours');

            // Only auto-calculate hours for attendance logs (time fields exist).
            if (timeInInput && timeOutInput && hoursInput) {
                function calculateHours() {
                    const timeIn = timeInInput.value;
                    const timeOut = timeOutInput.value;

                    if (timeIn && timeOut) {
                        const start = new Date(`2026-01-01T${timeIn}:00`);
                        let end = new Date(`2026-01-01T${timeOut}:00`);

                        // If time out is earlier than time in, assume it's next day (night shift)
                        if (end < start) {
                            end = new Date(`2026-01-02T${timeOut}:00`);
                        }

                        const diffInMs = end - start;
                        const diffInHrs = diffInMs / (1000 * 60 * 60);

                        // Update the hours input field
                        hoursInput.value = diffInHrs.toFixed(2);
                    } else {
                        hoursInput.value = '0.00';
                    }
                }

                timeInInput.addEventListener('change', calculateHours);
                timeOutInput.addEventListener('change', calculateHours);

                // Initial calculation if values exist
                calculateHours();
            }

            // Submit worklog function
            window.submitWorkLog = function() {
                const form = document.querySelector('form');
                if (form) {
                    // Create a submit button to trigger form submission
                    const submitBtn = document.createElement('button');
                    submitBtn.type = 'submit';
                    submitBtn.style.display = 'none';
                    
                    // Add data attribute to indicate this is a submit action
                    submitBtn.setAttribute('data-action', 'submit-worklog');
                    
                    form.appendChild(submitBtn);
                    
                    // Create form data from current form
                    const formData = new FormData(form);
                    
                    // Submit the form first (for saving changes)
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // After save succeeds, submit the worklog
                            const submitUrl = '{{ route("student.worklogs.submit", $workLog->id) }}';
                            return fetch(submitUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({})
                            });
                        } else {
                            throw new Error('Failed to save changes');
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Show success message
                            alert('Worklog saved and submitted successfully!');
                            // Redirect to dashboard
                            window.location.href = '{{ route("student.dashboard") }}';
                        } else {
                            throw new Error('Failed to submit worklog');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred: ' + error.message);
                    })
                    .finally(() => {
                        // Clean up
                        submitBtn.remove();
                    });
                }
            };
        });
    </script>
</x-student-layout>

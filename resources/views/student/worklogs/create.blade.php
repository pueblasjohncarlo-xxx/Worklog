<x-student-layout>
    <x-slot name="header">
        New {{ ucfirst($type) }} Accomplishment Report
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">
                        {{ ucfirst($type) }} Report Form
                    </h3>
                    <div class="flex gap-2">
                        <a href="{{ route('student.worklogs.create', ['type' => 'daily']) }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $type === 'daily' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Daily</a>
                        <a href="{{ route('student.worklogs.create', ['type' => 'weekly']) }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $type === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Weekly</a>
                        <a href="{{ route('student.worklogs.create', ['type' => 'monthly']) }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $type === 'monthly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Monthly</a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('student.worklogs.store') }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="space-y-1">
                        <label for="work_date" class="block text-sm font-medium">
                            @if($type === 'daily') Date @elseif($type === 'weekly') Week Ending Date @else Month Ending Date @endif
                        </label>
                        @if($type === 'daily')
                            @php
                                $allowed = isset($approvedDates) ? collect($approvedDates)->map->toDateString() : collect();
                            @endphp
                            @if($allowed->isEmpty())
                                <div class="mt-1 px-4 py-3 rounded-md bg-yellow-50 text-yellow-700 text-sm">
                                    Walay available nga approved attendance dates para sa journal.
                                </div>
                            @else
                                <select
                                    id="work_date"
                                    name="work_date"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach($allowed as $d)
                                        <option value="{{ $d }}" @selected(old('work_date', $date) === $d)>
                                            {{ \Carbon\Carbon::parse($d)->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($attendance)
                                    <p class="text-[10px] text-emerald-600 font-bold uppercase mt-1">
                                        Present: {{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }} - {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Ongoing' }}
                                    </p>
                                @endif
                            @endif
                        @else
                            <input
                                id="work_date"
                                name="work_date"
                                type="date"
                                value="{{ old('work_date', $date) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        @endif
                    </div>

                    @if($type === 'daily')
                    <div class="space-y-1">
                        <label for="hours" class="block text-sm font-medium">
                            Hours Rendered
                        </label>
                        <input
                            id="hours"
                            name="hours"
                            type="number"
                            step="0.25"
                            min="0"
                            max="24"
                            value="{{ old('hours', '8') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    @else
                        <input type="hidden" name="hours" value="0">
                    @endif

                    <div class="rounded-xl border border-indigo-200/60 dark:border-indigo-800/50 bg-indigo-50/70 dark:bg-indigo-900/15 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-widest text-indigo-700 dark:text-indigo-300">Template-based submission</p>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-1">
                                    Download the official {{ ucfirst($type) }} template, fill it out in Word/Google Docs, then upload the completed file as your accomplishment report.
                                </p>
                            </div>
                            <a
                                id="downloadTemplateBtn"
                                href="{{ route('student.accomplishment-reports.template', ['type' => $type, 'work_date' => old('work_date', $date), 'hours' => old('hours', '8')]) }}"
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
                                <div class="text-sm font-extrabold text-gray-800 dark:text-gray-100">{{ strtoupper($type) }}</div>
                            </div>
                            <div class="rounded-lg bg-white/80 dark:bg-black/20 border border-white/60 dark:border-white/10 p-3 md:col-span-2">
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Covered period</div>
                                <div id="coveredPeriod" class="text-sm font-extrabold text-gray-800 dark:text-gray-100"></div>
                                <div class="text-[11px] text-gray-600 dark:text-gray-300 mt-1">
                                    Weekly = Monday–Friday of the selected date’s week. Monthly = entire month of the selected date.
                                </div>
                            </div>
                        </div>

                        <ol class="list-decimal list-inside text-[12px] text-gray-700 dark:text-gray-200 space-y-1">
                            <li>Download the correct template.</li>
                            <li>Fill in your accomplishments outside the system.</li>
                            <li>Upload the completed file below and submit.</li>
                        </ol>
                    </div>

                    <div class="space-y-1">
                        <label for="attachment" class="block text-sm font-medium">
                            Upload Completed Accomplishment Report (Required)
                        </label>
                        <input
                            id="attachment"
                            name="attachment"
                            type="file"
                            required
                            accept=".doc,.docx,.pdf,.odt"
                            class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:uppercase file:tracking-widest file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 dark:file:bg-indigo-900 dark:file:text-indigo-200"
                        >
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                            Accepted: DOC, DOCX, ODT, PDF (max 10MB).
                        </p>
                    </div>

                    <script>
                        (function () {
                            const type = @json($type);
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
                                const day = date.getUTCDay(); // 0=Sun
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

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a
                            href="{{ route('student.journal.index') }}"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                        >
                            Cancel
                        </a>
                        @php $disableSubmit = ($type === 'daily') && (isset($approvedDates) && collect($approvedDates)->isEmpty()); @endphp
                        <button
                            type="submit"
                            @if($disableSubmit) disabled @endif
                            class="inline-flex items-center px-6 py-3 rounded-xl bg-indigo-600 text-sm font-bold uppercase tracking-widest text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-student-layout>

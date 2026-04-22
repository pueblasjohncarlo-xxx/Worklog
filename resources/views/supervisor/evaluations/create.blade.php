<x-supervisor-layout>
    <x-slot name="header">
        New Performance Evaluation
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6" x-data="evaluationTemplateFlow()">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-extrabold mb-2 text-gray-900 dark:text-white">Template-Based Submission</h2>
            <p class="mb-6 text-sm font-medium text-gray-700 dark:text-gray-300">
                Download the official performance evaluation template, complete it externally (Word/Docs), upload the finished file, and submit.
            </p>

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('supervisor.evaluations.store') }}" class="space-y-7" x-ref="form" @submit.prevent="openConfirm()" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="confirm_submission" value="1">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <x-input-label for="student_id" :value="__('Select Student')" class="text-gray-900 dark:text-white font-bold" />
                    <select id="student_id" name="student_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white text-gray-900 font-semibold dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Choose a student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ (string) old('student_id', $selectedStudentId ?? '') === (string) $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="evaluation_date" :value="__('Evaluation Date')" class="text-gray-900 dark:text-white font-bold" />
                    <x-text-input id="evaluation_date" class="block mt-1 w-full text-gray-900 font-semibold dark:text-white" type="date" name="evaluation_date" :value="old('evaluation_date', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('evaluation_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="semester" :value="__('Evaluation Period & Type')" class="text-gray-900 dark:text-white font-bold" />
                    <select id="semester" name="semester" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white text-gray-900 font-semibold dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Select Type --</option>
                        @foreach(['1st Semester', '2nd Semester', 'Summer'] as $period)
                            <optgroup label="{{ $period }}">
                                <option value="{{ $period }} (Weekly)" {{ old('semester') === "$period (Weekly)" ? 'selected' : '' }}>{{ $period }} - Weekly</option>
                                <option value="{{ $period }} (Monthly)" {{ old('semester') === "$period (Monthly)" ? 'selected' : '' }}>{{ $period }} - Monthly</option>
                                <option value="{{ $period }}" {{ old('semester') === $period ? 'selected' : '' }}>{{ $period }} - Final</option>
                            </optgroup>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50/70 dark:bg-indigo-900/20 p-5 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-widest text-indigo-700 dark:text-indigo-300">Step 1</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Download the official evaluation template</p>
                    </div>
                    <a id="downloadTemplateBtn"
                       href="{{ route('supervisor.evaluations.template', ['student_id' => old('student_id', $selectedStudentId ?? ''), 'evaluation_date' => old('evaluation_date', date('Y-m-d')), 'semester' => old('semester', '')]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-extrabold uppercase tracking-widest hover:bg-indigo-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                        </svg>
                        Download Template
                    </a>
                </div>

                <ol class="list-decimal list-inside text-[13px] text-gray-700 dark:text-gray-200 space-y-1">
                    <li>Select student, date, and period first.</li>
                    <li>Download and complete the template externally.</li>
                    <li>Upload the finished document and submit after review.</li>
                </ol>
            </div>

            <div class="space-y-1">
                <x-input-label for="attachment" :value="__('Step 2: Upload Completed Evaluation File')" class="text-gray-900 dark:text-white font-bold" />
                <input
                    id="attachment"
                    name="attachment"
                    type="file"
                    required
                    accept=".doc,.docx,.odt,.pdf"
                    class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:uppercase file:tracking-widest file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 dark:file:bg-indigo-900 dark:file:text-indigo-200"
                >
                <p class="text-[11px] text-gray-600 dark:text-gray-400 font-medium">Accepted: DOC, DOCX, ODT, PDF (max {{ $maxUploadMb }}MB).</p>
                <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
            </div>

            <div x-data="{
                maxWords: 1000,
                text: @js(old('remarks', '')),
                get count(){ return (this.text.trim().match(/\\S+/g) || []).length },
                enforce(e){ 
                    const words = this.text.trim().match(/\\S+/g) || [];
                    if(words.length > this.maxWords){
                        this.text = words.slice(0, this.maxWords).join(' ');
                        e.preventDefault();
                    }
                }
            }">
                <x-input-label for="remarks" :value="__('Additional Remarks / Comments')" class="text-gray-900 dark:text-white font-bold" />
                <textarea id="remarks" name="remarks" rows="6"
                    x-model="text"
                    @input="enforce($event)"
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white text-gray-900 font-semibold dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Write up to 1000 words..."></textarea>
                <div class="mt-1 text-xs" :class="count > maxWords ? 'text-rose-600' : 'text-gray-500 dark:text-gray-400'">
                    <span x-text="count"></span> / 1000 words
                </div>
                <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('supervisor.evaluations.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline mr-4">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-indigo-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:bg-indigo-500 dark:focus:bg-indigo-400 active:bg-indigo-700 dark:active:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 cursor-pointer">
                    Submit Evaluation
                </button>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Evaluation</h3>
                    <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" @click="show=false" aria-label="Close">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500">Student</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="preview.student"></div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500">Date</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="preview.date"></div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500">Semester / Type</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="preview.semester"></div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500">Uploaded File</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="preview.file"></div>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">Remarks</div>
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 max-h-40 overflow-auto" x-text="preview.remarks || '—'"></div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                    <button class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-semibold" @click="show=false">Edit</button>
                    <button class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-bold" @click="$refs.form.submit()">Confirm & Submit</button>
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>

<script>
function evaluationTemplateFlow(){
    return {
        show: false,
        preview: { student:'', date:'', semester:'', file:'', remarks:'' },
        openConfirm(){
            const form = this.$refs.form;
            const sel = form.querySelector('#student_id');
            this.preview.student = sel && sel.selectedIndex > 0 ? sel.options[sel.selectedIndex].text.trim() : '(No student selected)';

            const dateEl = form.querySelector('#evaluation_date');
            this.preview.date = dateEl ? dateEl.value : '';

            const semesterEl = form.querySelector('#semester');
            this.preview.semester = semesterEl && semesterEl.value ? semesterEl.value : '(No period selected)';

            const fileEl = form.querySelector('#attachment');
            this.preview.file = (fileEl && fileEl.files && fileEl.files.length > 0)
                ? `${fileEl.files[0].name} (${(fileEl.files[0].size / 1024 / 1024).toFixed(2)} MB)`
                : '(No file selected)';

            const remarksEl = form.querySelector('#remarks');
            this.preview.remarks = remarksEl ? remarksEl.value : '';

            if (!sel || sel.selectedIndex <= 0 || !this.preview.date || !this.preview.semester || this.preview.file === '(No file selected)') {
                alert('Please complete student, date, semester/type, and file upload before submission.');
                return;
            }

            this.show = true;
        }
    }
}

(function () {
    const studentEl = document.getElementById('student_id');
    const dateEl = document.getElementById('evaluation_date');
    const semesterEl = document.getElementById('semester');
    const downloadBtn = document.getElementById('downloadTemplateBtn');
    const baseUrl = @json(route('supervisor.evaluations.template'));

    function updateTemplateHref() {
        if (!downloadBtn) return;
        const params = new URLSearchParams();
        if (studentEl?.value) params.set('student_id', studentEl.value);
        if (dateEl?.value) params.set('evaluation_date', dateEl.value);
        if (semesterEl?.value) params.set('semester', semesterEl.value);
        downloadBtn.href = baseUrl + '?' + params.toString();
    }

    studentEl?.addEventListener('change', updateTemplateHref);
    dateEl?.addEventListener('change', updateTemplateHref);
    semesterEl?.addEventListener('change', updateTemplateHref);
    updateTemplateHref();
})();
</script>

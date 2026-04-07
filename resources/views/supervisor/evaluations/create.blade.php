<x-supervisor-layout>
    <x-slot name="header">
        Evaluate Student Performance
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md" x-data="evaluationConfirm()">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">New Evaluation Form</h2>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">Rate the student on a scale of 1 to 5 (1 = Poor, 5 = Excellent).</p>

        <form method="POST" action="{{ route('supervisor.evaluations.store') }}" class="space-y-8" x-ref="form" @submit.prevent="openConfirm($event)">
            @csrf

            <!-- Student Selection & Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="student_id" :value="__('Select Student')" class="text-gray-900 dark:text-white font-bold" />
                    <select id="student_id" name="student_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white text-gray-900 font-semibold dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Choose a student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
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
            </div>

            <!-- Evaluation Period & Type -->
            <div class="max-w-md">
                <x-input-label for="semester" :value="__('Evaluation Period & Type')" class="text-gray-900 dark:text-white font-bold" />
                <select id="semester" name="semester" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white text-gray-900 font-semibold dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">-- Select Type --</option>
                    @foreach(['1st Semester', '2nd Semester', 'Summer'] as $period)
                        <optgroup label="{{ $period }}">
                            <option value="{{ $period }} (Weekly)" {{ old('semester') === "$period (Weekly)" ? 'selected' : '' }}>
                                {{ $period }} - Weekly
                            </option>
                            <option value="{{ $period }} (Monthly)" {{ old('semester') === "$period (Monthly)" ? 'selected' : '' }}>
                                {{ $period }} - Monthly
                            </option>
                            <option value="{{ $period }}" {{ old('semester') === $period ? 'selected' : '' }}>
                                {{ $period }} - Final
                            </option>
                        </optgroup>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">Please select the specific semester and whether this is a weekly, monthly, or final evaluation.</p>
            </div>

            <!-- Rating Section -->
            <div class="space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Performance Ratings</h3>
                
                @php
                    $criteria = [
                        'attendance_punctuality' => 'Attendance & Punctuality (Regularity and timeliness)',
                        'quality_of_work' => 'Quality of Work (Accuracy, neatness, thoroughness)',
                        'initiative' => 'Initiative (Self-starter, resourceful)',
                        'cooperation' => 'Cooperation (Ability to work with others)',
                        'dependability' => 'Dependability (Reliability in completing tasks)',
                        'communication_skills' => 'Communication Skills (Verbal and written)',
                    ];
                @endphp

                @foreach($criteria as $key => $label)
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 w-full md:w-1/2">{{ $label }}</label>
                        <div class="flex items-center gap-4 w-full md:w-1/2 justify-between">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="flex flex-col items-center cursor-pointer">
                                    <input type="radio" name="{{ $key }}" value="{{ $i }}" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old($key) == $i ? 'checked' : '' }} required>
                                    <span class="text-xs mt-1 text-gray-500">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get($key)" class="mt-1" />
                @endforeach
            </div>

            <!-- Remarks -->
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
                <x-primary-button>
                    {{ __('Submit Evaluation') }}
                </x-primary-button>
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
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left">Criteria</th>
                                    <th class="px-4 py-2 text-left">Rating</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                                <template x-for="row in preview.rows" :key="row.k">
                                    <tr>
                                        <td class="px-4 py-2" x-text="row.label"></td>
                                        <td class="px-4 py-2 font-semibold" x-text="row.val"></td>
                                    </tr>
                                </template>
                                <tr class="bg-indigo-50 dark:bg-indigo-900/40">
                                    <td class="px-4 py-2 font-bold">Final Rating</td>
                                    <td class="px-4 py-2 font-extrabold text-indigo-700 dark:text-indigo-300" x-text="preview.avg.toFixed(2) + ' / 5.00'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">Remarks</div>
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 max-h-40 overflow-auto" x-text="preview.remarks || '—'"></div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                    <button class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-semibold" @click="show=false">Edit</button>
                    <button class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-bold" @click="$refs.form.submit()">Confirm & Send</button>
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>

<script>
function evaluationConfirm(){
    return {
        show: false,
        preview: { student:'', date:'', remarks:'', avg:0, rows:[] },
        openConfirm(e){
            const form = this.$refs.form;
            // Student
            const sel = form.querySelector('#student_id');
            this.preview.student = sel && sel.selectedIndex > 0 ? sel.options[sel.selectedIndex].text.trim() : '(No student selected)';
            // Date
            const dateEl = form.querySelector('#evaluation_date');
            this.preview.date = dateEl ? dateEl.value : '';
            // Remarks
            const remarksEl = form.querySelector('#remarks');
            this.preview.remarks = remarksEl ? remarksEl.value : '';
            // Ratings
            const keys = [
                ['attendance_punctuality','Attendance & Punctuality'],
                ['quality_of_work','Quality of Work'],
                ['initiative','Initiative'],
                ['cooperation','Cooperation'],
                ['dependability','Dependability'],
                ['communication_skills','Communication Skills']
            ];
            let sum = 0, cnt = 0;
            this.preview.rows = keys.map(([k,label])=>{
                const checked = form.querySelector(`input[name=\"${k}\"]:checked`);
                const val = checked ? parseInt(checked.value) : 0;
                if(val){ sum += val; cnt++; }
                return { k, label, val: val ? (val + ' / 5') : '—' };
            });
            this.preview.avg = cnt ? (sum / cnt) : 0;
            this.show = true;
        }
    }
}
</script>

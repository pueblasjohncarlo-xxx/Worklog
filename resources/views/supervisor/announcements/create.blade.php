<x-supervisor-layout>
    <x-slot name="header">
        Create Announcement
    </x-slot>

    @php
        $selectedRecipientIds = collect(old('recipient_ids', []))
            ->map(fn ($id) => (string) $id)
            ->values();
    @endphp

    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
        <style>
            .supervisor-announcement-form select,
            .supervisor-announcement-form option,
            .supervisor-announcement-form optgroup {
                color: #0f172a;
                background-color: #ffffff;
                font-weight: 600;
            }

            .supervisor-announcement-form input::placeholder,
            .supervisor-announcement-form textarea::placeholder {
                color: #64748b;
                opacity: 1;
            }
        </style>

        <h2 class="text-2xl font-black mb-2 text-slate-950 dark:text-slate-100">Compose New Message</h2>
        <p class="mb-6 text-sm font-medium leading-6 text-slate-700 dark:text-slate-300">
            Post an announcement to one or more assigned OJT students. Attachments and posting behavior stay the same; only the selected students will receive this message.
        </p>

        <form method="POST" action="{{ route('supervisor.announcements.store') }}" class="supervisor-announcement-form space-y-6" enctype="multipart/form-data">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Title')" class="font-bold text-slate-900 dark:text-slate-100" />
                <x-text-input id="title" class="block mt-1 w-full text-slate-900 font-semibold dark:text-slate-100" type="text" name="title" :value="old('title')" required autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="type" :value="__('Type')" class="font-bold text-slate-900 dark:text-slate-100" />
                <select id="type" name="type" class="block mt-1 w-full rounded-md shadow-sm border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 text-slate-900 font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="announcement" @selected(old('type', 'announcement') === 'announcement')>Announcement</option>
                    <option value="update" @selected(old('type') === 'update')>Update</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <input type="hidden" name="audience" value="students">

            <div
                x-data="supervisorRecipientPicker(@js($assignedStudents), @js($selectedRecipientIds))"
                class="space-y-3"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <x-input-label for="recipient-search" :value="__('Send To')" class="font-bold text-slate-900 dark:text-slate-100" />
                        <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-300">
                            Search and choose one or more assigned OJT students. Only selected recipients will receive this announcement.
                        </p>
                    </div>
                    <div class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-black uppercase tracking-[0.16em] text-slate-700 ring-1 ring-slate-200 dark:bg-slate-700 dark:text-slate-100 dark:ring-slate-600">
                        <span x-text="selectedCount"></span>&nbsp;selected
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/90 p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/50">
                    @if($assignedStudents->isEmpty())
                        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-4 text-sm font-semibold text-amber-900 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-100">
                            No assigned OJT students are available in your active roster yet.
                        </div>
                    @else
                        <div class="flex flex-col gap-3 md:flex-row md:items-center">
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                </svg>
                                <input
                                    id="recipient-search"
                                    type="text"
                                    x-model="query"
                                    class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-10 pr-4 text-sm font-semibold text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100"
                                    placeholder="Search by student name, email, section, or company"
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="selectVisible()" class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:bg-black focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-slate-100 dark:text-slate-950 dark:hover:bg-white">
                                    Select Visible
                                </button>
                                <button type="button" @click="clearSelected()" class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 ring-1 ring-slate-300 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-600 dark:hover:bg-slate-700">
                                    Clear
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 max-h-72 space-y-2 overflow-y-auto pr-1">
                            <template x-for="student in filteredStudents" :key="student.id">
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-left shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50/50 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-indigo-500/50 dark:hover:bg-slate-800">
                                    <input
                                        type="checkbox"
                                        name="recipient_ids[]"
                                        :value="student.id"
                                        x-model="selected"
                                        class="mt-1 h-4 w-4 rounded border-slate-400 text-indigo-600 focus:ring-indigo-500"
                                    >
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-black text-slate-950 dark:text-slate-100" x-text="student.name"></span>
                                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-200" x-text="student.section || 'No section'"></span>
                                        </div>
                                        <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-300" x-text="student.email || 'No email listed'"></p>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">
                                            Company:
                                            <span class="text-slate-700 dark:text-slate-200" x-text="student.company || 'No company assigned'"></span>
                                        </p>
                                    </div>
                                </label>
                            </template>

                            <div x-show="filteredStudents.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center text-sm font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">
                                No assigned students match your search.
                            </div>
                        </div>

                        <div x-show="selectedStudents.length > 0" class="mt-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-600 dark:text-slate-300">Selected Recipients</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="student in selectedStudents" :key="`selected-${student.id}`">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1.5 text-xs font-bold text-white dark:bg-slate-100 dark:text-slate-950">
                                        <span x-text="student.name"></span>
                                        <button type="button" class="rounded-full bg-white/15 px-1.5 py-0.5 text-[10px] font-black text-white dark:bg-slate-300 dark:text-slate-950" @click="toggle(student.id)">X</button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    @endif
                </div>

                <x-input-error :messages="$errors->get('recipient_ids')" class="mt-2" />
                <x-input-error :messages="$errors->get('recipient_ids.*')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="content" :value="__('Message Content')" class="font-bold text-slate-900 dark:text-slate-100" />
                <textarea id="content" name="content" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-slate-900 font-semibold focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('content') }}</textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="attachment" :value="__('Attachment (Optional)')" class="font-bold text-slate-900 dark:text-slate-100" />
                <input id="attachment" name="attachment" type="file" class="block w-full text-base font-semibold text-slate-700 dark:text-slate-200 file:mr-4 file:rounded-full file:border-0 file:bg-indigo-100 file:px-4 file:py-2 file:text-sm file:font-black file:text-indigo-800 hover:file:bg-indigo-200 dark:file:bg-indigo-500/20 dark:file:text-indigo-200" />
                <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Allowed files: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP (Max: 10MB)</p>
                <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('supervisor.announcements.index') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-950 dark:hover:text-slate-100 underline mr-4">
                    Cancel
                </a>
                <x-primary-button :disabled="$assignedStudents->isEmpty()">
                    {{ __('Post Announcement') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function supervisorRecipientPicker(students, oldSelected) {
            const normalizedStudents = Array.isArray(students) ? students.map((student) => ({
                ...student,
                id: String(student.id),
            })) : [];

            return {
                students: normalizedStudents,
                selected: Array.isArray(oldSelected) ? oldSelected.map((id) => String(id)) : [],
                query: '',
                get filteredStudents() {
                    const needle = this.query.trim().toLowerCase();

                    if (!needle) {
                        return this.students;
                    }

                    return this.students.filter((student) => {
                        return [
                            student.name,
                            student.email,
                            student.section,
                            student.company,
                        ].join(' ').toLowerCase().includes(needle);
                    });
                },
                get selectedStudents() {
                    return this.students.filter((student) => this.selected.includes(student.id));
                },
                get selectedCount() {
                    return this.selected.length;
                },
                selectVisible() {
                    const merged = new Set(this.selected);
                    this.filteredStudents.forEach((student) => merged.add(student.id));
                    this.selected = Array.from(merged);
                },
                clearSelected() {
                    this.selected = [];
                },
                toggle(studentId) {
                    const key = String(studentId);
                    this.selected = this.selected.filter((selectedId) => selectedId !== key);
                },
            };
        }
    </script>
</x-supervisor-layout>

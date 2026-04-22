<x-student-layout>
    <x-slot name="header">
        Mapping
    </x-slot>

    <style>
        @media print {
            .app-sidebar, header, nav, .no-print { display: none !important; }
            main { padding: 0 !important; }
            .print-reset { box-shadow: none !important; border: none !important; }
            body { background: #fff !important; }
        }
    </style>

    <div class="space-y-6">
        <div class="student-light-card p-6 print-reset">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Mapping of OJT Hours</h2>
                    <p class="text-sm font-medium text-slate-600">Attendance-based monthly summary paired with your accomplishment reports.</p>
                </div>

                <div class="no-print flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                        <div>
                            <label for="from" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">From</label>
                            <input id="from" name="from" type="month" value="{{ $fromKey }}" class="mt-1 rounded-md border-slate-300 bg-white text-slate-900" />
                        </div>
                        <div>
                            <label for="to" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">To</label>
                            <input id="to" name="to" type="month" value="{{ $toKey }}" class="mt-1 rounded-md border-slate-300 bg-white text-slate-900" />
                        </div>
                        <button type="submit" class="h-[42px] px-4 rounded-md bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">Apply</button>
                    </form>
                    <button type="button" onclick="window.print()" class="h-[42px] px-4 rounded-md bg-gray-900 text-white text-sm font-bold hover:bg-black">Print</button>
                </div>
            </div>

            @if(!$assignment)
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    No active assignment found. Mapping will appear once you have an active OJT assignment.
                </div>
            @else
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                        <div><span class="font-bold">Student Name:</span> {{ $assignment->student?->name ?? '—' }}</div>
                        <div><span class="font-bold">Section:</span> {{ $assignment->student?->normalizedStudentSection() ?? ($assignment->student?->section ?? '—') }}</div>
                        <div><span class="font-bold">Company:</span> {{ $assignment->company?->name ?? '—' }}</div>
                        <div><span class="font-bold">Department:</span> {{ $assignment->student?->department ?? ($assignment->student?->studentProfile?->program ?? '—') }}</div>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                        <div><span class="font-bold">Course/Program:</span> {{ $assignment->student?->studentProfile?->program ?? '—' }}</div>
                        <div><span class="font-bold">Student No.:</span> {{ $assignment->student?->studentProfile?->student_number ?? '—' }}</div>
                        <div><span class="font-bold">Date Submitted:</span> {{ $submittedAt->format('F d, Y') }}</div>
                    </div>
                </div>
            @endif
        </div>

        @if($assignment && $mapping)
            @include('partials.mapping.calendar-range', ['mapping' => $mapping])
        @endif
    </div>
</x-student-layout>

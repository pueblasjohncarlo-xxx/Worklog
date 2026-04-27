<x-student-layout>
    <x-slot name="header">
        Mapping
    </x-slot>

    <style>
        @media print {
            .app-sidebar, header, nav, .no-print { display: none !important; }
            main { padding: 0 !important; }
            .print-reset { box-shadow: none !important; border: none !important; }
            body { background: #fff !important; color: #0f172a !important; }
        }
    </style>

    <div class="space-y-6">
        <div class="student-light-card p-6 print-reset">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-950">Mapping of OJT Hours</h2>
                    <p class="text-sm font-semibold text-slate-700">Attendance-based monthly summary paired with your accomplishment reports.</p>
                </div>

                <div class="no-print flex flex-col items-stretch gap-3 sm:flex-row sm:items-end">
                    <form method="GET" class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-end">
                        <div>
                            <label for="from" class="block text-xs font-black uppercase tracking-wider text-slate-700">From</label>
                            <input id="from" name="from" type="month" value="{{ $fromKey }}" class="mt-1 rounded-md border-slate-300 bg-white text-slate-900" />
                        </div>
                        <div>
                            <label for="to" class="block text-xs font-black uppercase tracking-wider text-slate-700">To</label>
                            <input id="to" name="to" type="month" value="{{ $toKey }}" class="mt-1 rounded-md border-slate-300 bg-white text-slate-900" />
                        </div>
                        <button type="submit" class="h-[42px] rounded-md bg-indigo-600 px-4 text-sm font-bold text-white hover:bg-indigo-700">Apply</button>
                    </form>
                    @if($assignment && $mapping)
                        <a href="{{ route('student.mapping.export', ['from' => $fromKey, 'to' => $toKey, 'format' => 'pdf']) }}" class="inline-flex h-[42px] items-center justify-center rounded-md bg-rose-600 px-4 text-sm font-bold text-white hover:bg-rose-700">
                            Export PDF
                        </a>
                        <a href="{{ route('student.mapping.export', ['from' => $fromKey, 'to' => $toKey, 'format' => 'doc']) }}" class="inline-flex h-[42px] items-center justify-center rounded-md bg-sky-600 px-4 text-sm font-bold text-white hover:bg-sky-700">
                            Export Word
                        </a>
                    @endif
                    <button type="button" onclick="window.print()" class="h-[42px] rounded-md bg-gray-900 px-4 text-sm font-bold text-white hover:bg-black">Print</button>
                </div>
            </div>

            @if(!$assignment)
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-950">
                    No active assignment found. Mapping will appear once you have an active OJT assignment.
                </div>
            @else
                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-2 text-sm text-slate-800">
                        <div><span class="font-black text-slate-950">Student Name:</span> <span class="font-semibold">{{ $assignment->student?->name ?? '-' }}</span></div>
                        <div><span class="font-black text-slate-950">Section:</span> <span class="font-semibold">{{ $assignment->student?->normalizedStudentSection() ?? ($assignment->student?->section ?? '-') }}</span></div>
                        <div><span class="font-black text-slate-950">Company:</span> <span class="font-semibold">{{ $assignment->company?->name ?? '-' }}</span></div>
                        <div><span class="font-black text-slate-950">Department:</span> <span class="font-semibold">{{ $assignment->student?->department ?? ($assignment->student?->studentProfile?->program ?? '-') }}</span></div>
                    </div>
                    <div class="space-y-2 text-sm text-slate-800">
                        <div><span class="font-black text-slate-950">Course/Program:</span> <span class="font-semibold">{{ $assignment->student?->studentProfile?->program ?? '-' }}</span></div>
                        <div><span class="font-black text-slate-950">Student No.:</span> <span class="font-semibold">{{ $assignment->student?->studentProfile?->student_number ?? '-' }}</span></div>
                        <div><span class="font-black text-slate-950">Date Submitted:</span> <span class="font-semibold">{{ $submittedAt->format('F d, Y') }}</span></div>
                    </div>
                </div>
            @endif
        </div>

        @if($assignment && $mapping)
            @include('partials.mapping.calendar-range', ['mapping' => $mapping])
        @endif
    </div>
</x-student-layout>

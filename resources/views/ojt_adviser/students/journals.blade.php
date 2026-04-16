<x-ojt-adviser-layout>
    <x-slot name="header">
        Journals & Reflections: {{ $student->name }}
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('ojt_adviser.students') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold flex items-center gap-2 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to OJT Students
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($workLogs as $log)
                <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
                    <div class="p-6 border-b border-white/10 bg-black/20 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <span class="text-indigo-400 font-bold">{{ $log->work_date->format('M d, Y') }}</span>
                            <span class="px-2 py-0.5 rounded bg-white/10 text-gray-400 text-[10px] font-bold uppercase tracking-widest">{{ $log->type }}</span>
                        </div>
                        @if($log->attachment_path)
                            <a href="{{ route('ojt_adviser.worklogs.attachment', $log->id) }}?inline=1" target="_blank" class="text-xs text-indigo-400 hover:underline flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                View Attachment
                            </a>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="prose prose-invert max-w-none text-gray-300">
                            {!! nl2br(e($log->description)) !!}
                        </div>

                        <!-- Adviser Comment Section -->
                        <div class="mt-8 pt-6 border-t border-white/10">
                            <h4 class="text-sm font-bold text-indigo-300 uppercase tracking-widest mb-4">Adviser Feedback</h4>
                            @if($log->adviser_comment)
                                <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-4 text-sm text-gray-300 italic mb-4">
                                    "{{ $log->adviser_comment }}"
                                </div>
                            @endif

                            <form action="{{ route('ojt_adviser.journals.comment', $log) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="comment" placeholder="Provide feedback or comments..." 
                                       class="flex-1 bg-black/30 border-white/10 rounded-xl px-4 py-2 text-sm text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors">
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white/5 border border-white/10 rounded-2xl p-12 text-center text-gray-500 italic shadow-xl backdrop-blur-md">
                    No journals found for this student.
                </div>
            @endforelse
        </div>

        @if($workLogs->hasPages())
            <div class="mt-6">
                {{ $workLogs->links() }}
            </div>
        @endif
    </div>
</x-ojt-adviser-layout>

<x-supervisor-layout>
    <x-slot name="header">
        Announcements & Updates
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold tracking-tight text-white">
                <span class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 shadow-sm ring-1 ring-slate-700/80 transition-colors dark:bg-slate-100 dark:text-slate-950 dark:ring-slate-300">
                    My Announcements
                </span>
            </h2>
            <a href="{{ route('supervisor.announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Announcement
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($announcements as $announcement)
                    <li x-data="{ openDetails: false }" class="relative">
                        <button type="button" @click="openDetails = true" class="block w-full px-4 py-4 text-left transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset active:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-indigo-400 dark:active:bg-gray-700/90 sm:px-6">
                            <div class="flex items-center justify-between gap-4">
                                <p class="truncate text-sm font-bold text-slate-900 dark:text-slate-100">
                                    {{ $announcement->title }}
                                </p>
                                <div class="ml-2 flex flex-shrink-0 items-center gap-2">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $announcement->type === 'announcement' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' }}">
                                        {{ ucfirst($announcement->type) }}
                                    </p>
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-slate-700 ring-1 ring-slate-200 dark:bg-slate-700 dark:text-slate-100 dark:ring-slate-600">
                                        Open
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        To: {{ ucfirst($announcement->audience) }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm font-medium text-slate-700 dark:text-slate-300 sm:mt-0">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p>
                                        Posted on <time datetime="{{ $announcement->created_at }}">{{ $announcement->created_at->format('M d, Y') }}</time>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 text-sm font-medium leading-6 text-slate-700 dark:text-slate-300">
                                {{ Str::limit($announcement->content, 150) }}
                            </div>
                        </button>

                        <div x-show="openDetails" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="announcement-title-{{ $announcement->id }}" role="dialog" aria-modal="true">
                            <div class="flex min-h-screen items-center justify-center px-4 py-6 text-center">
                                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="openDetails = false" aria-hidden="true"></div>
                                <div x-show="openDetails" x-transition class="relative inline-block w-full max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white text-left align-middle shadow-2xl dark:border-slate-700 dark:bg-slate-800">
                                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-700 dark:bg-slate-900/70">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="space-y-2">
                                                <h3 id="announcement-title-{{ $announcement->id }}" class="text-xl font-black text-slate-950 dark:text-slate-100">{{ $announcement->title }}</h3>
                                                <div class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 {{ $announcement->type === 'announcement' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' }}">
                                                        {{ ucfirst($announcement->type) }}
                                                    </span>
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 ring-1 ring-slate-200 dark:bg-slate-700 dark:text-slate-100 dark:ring-slate-600">
                                                        Audience: {{ ucfirst($announcement->audience) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <button @click="openDetails = false" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm ring-1 ring-slate-200 transition-colors hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
                                                <span class="sr-only">Close announcement details</span>
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-5 px-6 py-5">
                                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">
                                            Posted on <time datetime="{{ $announcement->created_at }}">{{ $announcement->created_at->format('F d, Y \a\t h:i A') }}</time>
                                        </div>

                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-800 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                                            {{ $announcement->content }}
                                        </div>

                                        @if($announcement->attachment)
                                            <div class="flex flex-wrap items-center gap-3">
                                                <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="inline-flex items-center rounded-lg bg-indigo-700 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900 dark:bg-indigo-400 dark:text-slate-950 dark:hover:bg-indigo-300 dark:focus:ring-offset-slate-800">
                                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    Download Attachment
                                                </a>
                                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $announcement->original_filename ?? 'Attached file' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        You haven't posted any announcements yet.
                    </li>
                @endforelse
            </ul>
        </div>
        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    </div>
</x-supervisor-layout>

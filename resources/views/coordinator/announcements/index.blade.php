<x-coordinator-layout>
    <x-slot name="header">
        Announcements & Updates
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold tracking-tight text-white">
                <span class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 shadow-sm ring-1 ring-slate-700/80 transition-colors dark:bg-slate-100 dark:text-slate-950 dark:ring-slate-300">
                    Broadcast Messages
                </span>
            </h2>
            <a href="{{ route('coordinator.announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                    <li>
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                    {{ $announcement->title }}
                                </p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $announcement->type === 'announcement' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($announcement->type) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        To: {{ ucfirst($announcement->audience) }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-700 dark:text-gray-200 sm:mt-0">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p>
                                        Posted on <time datetime="{{ $announcement->created_at }}">{{ $announcement->created_at->format('M d, Y') }}</time>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                {{ Str::limit($announcement->content, 150) }}
                            </div>
                            
                            @if($announcement->attachment)
                                <div class="mt-3">
                                    <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="inline-flex items-center rounded-lg border border-indigo-700 bg-indigo-50 px-3 py-2 text-sm font-bold text-indigo-900 shadow-sm transition-colors hover:bg-indigo-100 hover:border-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-indigo-400/70 dark:bg-indigo-900/30 dark:text-indigo-100 dark:hover:bg-indigo-900/50">
                                        <svg class="mr-1.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Download Attachment: {{ $announcement->original_filename ?? 'View File' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-gray-700 dark:text-gray-200">
                        No announcements yet. Click "New Announcement" to create one.
                    </li>
                @endforelse
            </ul>
        </div>
        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    </div>
</x-coordinator-layout>

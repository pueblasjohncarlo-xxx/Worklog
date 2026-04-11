<x-student-layout>
    <x-slot name="header">
        Announcements & Updates
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Latest News</h2>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($announcements as $announcement)
                    <li>
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if($announcement->user->role === 'coordinator')
                                        <span class="flex-shrink-0 inline-block h-8 w-8 rounded-full overflow-hidden bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs border border-purple-200">
                                            CO
                                        </span>
                                    @else
                                        <span class="flex-shrink-0 inline-block h-8 w-8 rounded-full overflow-hidden bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs border border-indigo-200">
                                            {{ substr($announcement->user->name, 0, 2) }}
                                        </span>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $announcement->title }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            By {{ $announcement->user->name }} ({{ ucfirst($announcement->user->role) }})
                                        </p>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $announcement->type === 'announcement' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($announcement->type) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">
                                    {{ $announcement->content }}
                                </div>
                            </div>
                            <div class="mt-4 flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-3">
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p>
                                        Posted on <time datetime="{{ $announcement->created_at }}">{{ $announcement->created_at->format('F d, Y \a\t h:i A') }}</time>
                                    </p>
                                </div>
                                
                                @if($announcement->attachment)
                                    <div>
                                        <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Download Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No announcements available at this time.
                    </li>
                @endforelse
            </ul>
        </div>
        @if($announcements && $announcements->count() > 0)
        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</x-student-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-3xl text-white leading-tight">
                Chats
            </h2>
            <div class="flex gap-2">
                <button class="p-2.5 bg-gray-700/50 hover:bg-gray-600 text-white rounded-full transition-colors shadow-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path>
                    </svg>
                </button>
                <a href="{{ route('messages.create') }}" class="p-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-colors shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto">
            <!-- Stories/Chat Heads Section -->
            <div class="px-4 mb-6">
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                    @php
                        $allUsers = \App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get();
                    @endphp
                    @foreach($allUsers as $availableUser)
                        <a href="{{ route('messages.show', $availableUser) }}" class="flex-shrink-0 group relative hover:opacity-80 transition-opacity" title="{{ $availableUser->name }}">
                            <div class="relative">
                                @if ($availableUser->profile_photo_path)
                                    <img src="{{ Storage::url($availableUser->profile_photo_path) }}" alt="{{ $availableUser->name }}" class="h-16 w-16 rounded-full object-cover shadow-md border-4 border-blue-500 hover:border-blue-400 transition-colors cursor-pointer">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md border-4 border-blue-500 hover:border-blue-400 transition-colors cursor-pointer">
                                        {{ substr($availableUser->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="absolute bottom-1 right-1 block h-4 w-4 rounded-full bg-green-500 ring-2 ring-gray-900 shadow-sm"></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('messages.index') }}" class="px-4 mb-4">
                <div class="relative">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search Messages"
                        class="w-full pl-10 pr-4 py-3 rounded-full bg-gray-800 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                        @input.debounce.200ms="$el.form.submit()">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </form>

            <!-- Chat List -->
            <div class="px-4">
                @if($contacts->isEmpty())
                    <div class="text-center py-20">
                        <div class="mx-auto h-20 w-20 bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-10 w-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">No messages yet</h3>
                        <p class="mt-1 text-sm text-gray-400">Start a conversation to see it here.</p>
                        <div class="mt-6">
                            <a href="{{ route('messages.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                                Start Conversation
                            </a>
                        </div>
                    </div>
                @else
                    <div class="space-y-1">
                        @foreach($contacts as $contact)
                            @php
                                $isUnread = $contact->last_message->receiver_id === Auth::id() && is_null($contact->last_message->read_at);
                            @endphp
                            <a href="{{ route('messages.show', $contact) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-800 transition-colors duration-150 cursor-pointer group">
                                <!-- Avatar -->
                                <div class="flex-shrink-0 relative">
                                    @if ($contact->profile_photo_path)
                                        <img src="{{ Storage::url($contact->profile_photo_path) }}" alt="{{ $contact->name }}" class="h-14 w-14 rounded-full object-cover shadow-md {{ $isUnread ? 'ring-2 ring-blue-500' : '' }}">
                                    @else
                                        <div class="h-14 w-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-md {{ $isUnread ? 'ring-2 ring-blue-500' : '' }}">
                                            {{ substr($contact->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="absolute bottom-0 right-0 block h-4 w-4 rounded-full bg-green-500 ring-2 ring-gray-900"></span>
                                </div>

                                <!-- Content -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-base {{ $isUnread ? 'font-bold' : 'font-medium' }} text-white truncate">
                                            {{ $contact->name }}
                                        </p>
                                        <p class="text-sm text-gray-400 whitespace-nowrap ml-2 flex-shrink-0">
                                            {{ $contact->last_message->created_at->shortAbsoluteDiffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm {{ $isUnread ? 'text-gray-300' : 'text-gray-500' }} truncate">
                                            @if($contact->last_message->sender_id === Auth::id())
                                                <span class="text-gray-500">You: </span>
                                            @endif
                                            {{ Str::limit($contact->last_message->body, 50) }}
                                        </p>
                                        @if($isUnread)
                                            <div class="flex-shrink-0 h-3 w-3 rounded-full bg-blue-500"></div>
                                        @else
                                            <svg class="flex-shrink-0 h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/><path d="M10 17l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

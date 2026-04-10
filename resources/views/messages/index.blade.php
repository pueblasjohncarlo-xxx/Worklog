<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-white leading-tight">
                💬 Chats
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('messages.create') }}" class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-colors shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 min-h-[calc(100vh-160px)]">
        <div class="max-w-2xl mx-auto">
            <!-- Stories Section (Your Story + User Stories) -->
            <div class="px-4 py-6 border-b border-gray-200 dark:border-gray-800">
                <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    <!-- Your Story -->
                    <div class="flex-shrink-0 group relative cursor-pointer">
                        <div class="h-24 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center border-4 border-gray-300 dark:border-gray-700 hover:border-indigo-500 transition-colors">
                            <div class="text-4xl">+</div>
                        </div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white text-center mt-2 truncate w-16">Your Story</p>
                    </div>

                    <!-- User Stories -->
                    @php
                        $suggestedUsers = \App\Models\User::where('id', '!=', Auth::id())->inRandomOrder()->limit(8)->get();
                    @endphp
                    @forelse($suggestedUsers as $availableUser)
                        <a href="{{ route('messages.show', $availableUser) }}" class="flex-shrink-0 group relative" title="{{ $availableUser->name }}">
                            <div class="h-24 w-16 rounded-2xl overflow-hidden border-4 border-gray-300 dark:border-gray-700 group-hover:border-indigo-500 transition-colors cursor-pointer shadow-md">
                                @if ($availableUser->profile_photo_path)
                                    <img src="{{ Storage::url($availableUser->profile_photo_path) }}" alt="{{ $availableUser->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                                        {{ substr($availableUser->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs font-semibold text-gray-900 dark:text-white text-center mt-2 truncate w-16">{{ substr($availableUser->name, 0, 10) }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">No users available</p>
                    @endforelse
                </div>
            </div>

            <!-- Search Bar -->
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                <form method="GET" action="{{ route('messages.index') }}" class="relative">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search Messenger" 
                        class="w-full px-4 py-2.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none dark:border dark:border-gray-700 focus:border-indigo-500 text-sm">
                    <svg class="h-5 w-5 text-gray-400 absolute right-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
            </div>

            <!-- Chat List -->
            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                @if($contacts->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="text-5xl mb-4">💬</div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No conversations yet</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Start a conversation with someone to see it here</p>
                        <a href="{{ route('messages.create') }}" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition-colors font-semibold">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            New Message
                        </a>
                    </div>
                @else
                    @foreach($contacts as $contact)
                        @php
                            $isUnread = $contact->last_message && $contact->last_message->receiver_id === Auth::id() && is_null($contact->last_message->read_at);
                        @endphp
                        <a href="{{ route('messages.show', $contact) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                @if ($contact->profile_photo_path)
                                    <img src="{{ Storage::url($contact->profile_photo_path) }}" alt="{{ $contact->name }}" class="h-14 w-14 rounded-full object-cover border-2 {{ $isUnread ? 'border-blue-500' : 'border-gray-200 dark:border-gray-700' }} group-hover:border-indigo-500 transition-colors">
                                @else
                                    <div class="h-14 w-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg border-2 {{ $isUnread ? 'border-blue-500' : 'border-gray-200 dark:border-gray-700' }} group-hover:border-indigo-500 transition-colors">
                                        {{ substr($contact->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="absolute bottom-0 right-0 block h-4 w-4 rounded-full bg-green-500 border-2 border-white dark:border-gray-900"></span>
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <h3 class="font-bold text-gray-900 dark:text-white {{ $isUnread ? 'font-bold' : 'font-semibold' }}">{{ $contact->name }}</h3>
                                    @if($contact->last_message)
                                        <span class="text-xs text-gray-600 dark:text-gray-400 flex-shrink-0">{{ $contact->last_message->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                                @if($contact->last_message)
                                    <p class="text-sm {{ $isUnread ? 'text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-600 dark:text-gray-400' }} truncate">
                                        @if($contact->last_message->sender_id === Auth::id())
                                            <span>You: </span>
                                        @endif
                                        @if($contact->last_message->attachment_path)
                                            📎 {{ $contact->last_message->attachment_name ?? 'Attachment' }}
                                        @else
                                            {{ Str::limit($contact->last_message->body, 40) }}
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <!-- Unread Indicator -->
                            @if($isUnread)
                                <div class="flex-shrink-0 h-3 w-3 rounded-full bg-blue-500 shadow-md"></div>
                            @endif
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

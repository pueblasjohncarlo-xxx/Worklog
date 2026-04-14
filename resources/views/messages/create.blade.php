<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('messages.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-900 dark:text-white">{{ __('New Message') }}</h2>
        </div>
    </x-slot>

    <div class="py-6 bg-white dark:bg-gray-900 min-h-[calc(100vh-160px)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Users Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-lg sticky top-32 overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-600/20 dark:to-purple-600/20 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest">Available Users</h3>
                        </div>
                        <div class="space-y-1 max-h-96 overflow-y-auto p-3">
                            @forelse($potentialRecipients as $recipient)
                                <button type="button" onclick="document.getElementById('receiver_id').value = '{{ $recipient->id }}'; document.getElementById('receiver_id').dispatchEvent(new Event('change')); document.getElementById('body').focus(); return false;"
                                    class="w-full text-left p-3 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $recipient->profile_photo_url }}" data-avatar-user-id="{{ $recipient->id }}" alt="{{ $recipient->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-500/50">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">
                                                {{ $recipient->name }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                                {{ ucfirst(str_replace('_', ' ', $recipient->role)) }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No users available</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Message Form -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="p-8 space-y-6">
                            <form action="{{ route('messages.store') }}" method="POST" x-data="{ selectedUser: null }">
                                @csrf
                                
                                <!-- Recipient Selection -->
                                <div>
                                    <label for="receiver_id" class="block text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Select Recipient</label>
                                    <select name="receiver_id" id="receiver_id" required 
                                        @change="selectedUser = $el.options[$el.selectedIndex]?.text || null"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-1 shadow-sm py-3 px-4 transition-all">
                                        <option value="" disabled selected class="text-gray-900 dark:text-gray-100">Choose a user to message...</option>
                                        @foreach($potentialRecipients as $recipient)
                                            <option value="{{ $recipient->id }}" class="text-gray-900 dark:text-gray-100">
                                                {{ $recipient->name }} ({{ ucfirst(str_replace('_', ' ', $recipient->role)) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Message Textarea -->
                                <div>
                                    <label for="body" class="block text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Your Message</label>
                                    <div class="relative">
                                        <textarea name="body" id="body" rows="8" 
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-1 shadow-sm py-4 px-4 text-base resize-none transition-all"
                                            placeholder="Type your message here... (Keep it professional and helpful)"
                                            maxlength="5000"></textarea>
                                        <div class="absolute bottom-3 right-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span id="charCount">0</span> / 5000
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('messages.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-colors font-semibold">
                                        Cancel
                                    </a>
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition-all font-bold transform hover:scale-105 shadow-lg flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('body').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });
    </script>
    @endpush
</x-app-layout>

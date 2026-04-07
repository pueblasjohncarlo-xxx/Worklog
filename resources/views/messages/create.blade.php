<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Active Users Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-20">
                        <div class="p-4">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Available Users</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($potentialRecipients as $recipient)
                                    <a href="#" onclick="document.getElementById('receiver_id').value = '{{ $recipient->id }}'; document.getElementById('receiver_id').dispatchEvent(new Event('change')); return false;"
                                        class="block p-3 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors group">
                                        <div class="flex items-center gap-2">
                                            @if ($recipient->profile_photo_path)
                                                <img src="{{ Storage::url($recipient->profile_photo_path) }}" alt="{{ $recipient->name }}" class="h-8 w-8 rounded-full object-cover">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($recipient->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                    {{ $recipient->name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ ucfirst($recipient->role) }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No users available</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Form -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <form action="{{ route('messages.store') }}" method="POST" x-data="{ showPicker:false }">
                                @csrf
                                <div class="mb-4">
                                    <label for="receiver_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipient</label>
                                    <select name="receiver_id" id="receiver_id" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                        @change="document.querySelector('img[data-avatar-for]')?.classList.add('hidden')">
                                        <option value="" disabled selected>Select a recipient...</option>
                                        @foreach($potentialRecipients as $recipient)
                                            <option value="{{ $recipient->id }}" data-avatar="{{ $recipient->profile_photo_path ?? '' }}" data-role="{{ $recipient->role }}">
                                                {{ $recipient->name }} ({{ ucfirst($recipient->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                                    <div class="relative">
                                        <textarea x-ref="msg" name="body" id="body" rows="6" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-12" placeholder="Type your message here..."></textarea>
                                        <button type="button" @click="showPicker=!showPicker" class="absolute right-2 bottom-2 p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-300">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20Zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-4-7a1 1 0 0 1 .117 1.993L8 15H7a1 1 0 0 1-.117-1.993L7 13h1Zm10 0a1 1 0 0 1 .117 1.993L18 15h-1a1 1 0 0 1-.117-1.993L17 13h1ZM12 18c2.137 0 3.828-1.053 4.472-2.816a1 1 0 0 0-1.864-.768C14.279 15.584 13.35 16 12 16s-2.279-.416-2.608-1.584a1 1 0 1 0-1.864.768C8.172 16.947 9.863 18 12 18Z"/></svg>
                                        </button>
                                        <div x-show="showPicker" @click.outside="showPicker=false" class="absolute right-0 bottom-14 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-2 z-10" style="width: 360px; max-width: 90vw;">
                                            <emoji-picker id="emojiPickerCreate"></emoji-picker>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('messages.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                        Cancel
                                    </a>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
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
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <script>
        document.addEventListener('emoji-click', function(e){
            const picker = e.target;
            if(picker && picker.id === 'emojiPickerCreate'){
                const ta = document.getElementById('body');
                if(ta){
                    const emoji = e.detail.unicode;
                    const start = ta.selectionStart || ta.value.length;
                    const end = ta.selectionEnd || ta.value.length;
                    ta.value = ta.value.slice(0, start) + emoji + ta.value.slice(end);
                    ta.focus();
                    ta.selectionStart = ta.selectionEnd = start + emoji.length;
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

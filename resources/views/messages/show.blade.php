<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span>{{ $user->name }}</span>
                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full uppercase ml-2">{{ $user->role }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-6 h-[calc(100vh-160px)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-full flex flex-col">
                
                <!-- Messages Area -->
                <div class="flex-1 p-6 overflow-y-auto flex flex-col gap-4 bg-gray-50 dark:bg-gray-900" id="messages-container">
                    @foreach($messages as $message)
                        @php
                            $isMe = $message->sender_id === Auth::id();
                        @endphp
                        <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }} items-end gap-2">
                            
                            @if(!$isMe)
                                <div class="flex-shrink-0">
                                    @if($message->sender->profile_photo_path)
                                        <img class="h-8 w-8 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ Storage::url($message->sender->profile_photo_path) }}" alt="{{ $message->sender->name }}" />
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            {{ substr($message->sender->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="max-w-[70%] {{ $isMe ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-r-2xl rounded-tl-2xl shadow-sm border border-gray-100 dark:border-gray-700' }} px-4 py-3 relative group">
                                @if($message->attachment_path)
                                    <div class="mb-2">
                                        @if($message->attachment_type === 'image')
                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank">
                                                <img src="{{ Storage::url($message->attachment_path) }}" alt="Attachment" class="rounded-lg max-w-full h-auto max-h-64 object-cover hover:opacity-90 transition-opacity">
                                            </a>
                                        @elseif($message->attachment_type === 'video')
                                            <video controls class="rounded-lg max-w-full h-auto max-h-64">
                                                <source src="{{ Storage::url($message->attachment_path) }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center gap-2 p-3 rounded-lg {{ $isMe ? 'bg-indigo-700 hover:bg-indigo-800' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-colors">
                                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <div class="overflow-hidden">
                                                    <p class="text-sm font-medium truncate">{{ $message->attachment_name }}</p>
                                                    <p class="text-xs opacity-75">Click to download</p>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($message->body)
                                    <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->body }}</p>
                                @endif
                                
                        <p class="text-[10px] mt-1 {{ $isMe ? 'text-indigo-200' : 'text-gray-400' }} text-right opacity-70 group-hover:opacity-100 transition-opacity">
                                    {{ $message->created_at->format('M d, h:i A') }}
                                    @if($message->is_edited) <span class="ml-1 font-semibold">(edited)</span> @endif
                                </p>
                                
                                @if($message->read_at && $isMe)
                                    <p class="text-[9px] mt-0.5 text-indigo-300 text-right">✓ Read</p>
                                @endif
                            </div>

                            <!-- Message Actions -->
                            @if($isMe)
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex flex-col gap-1">
                                    @if($message->canEdit(Auth::id()))
                                        <button type="button" onclick="editMessage({{ $message->id }}, '{{ addslashes($message->body) }}')" class="p-1.5 text-gray-400 hover:text-indigo-400 hover:bg-gray-700/30 rounded transition-colors text-xs" title="Edit (5min window)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($message->canDelete(Auth::id()))
                                        <button type="button" onclick="deleteMessage({{ $message->id }})" class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-red-900/20 rounded transition-colors text-xs" title="Delete (60min window)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        

                            @if($isMe)
                                <div class="flex-shrink-0">
                                    @if(Auth::user()->profile_photo_path)
                                        <img class="h-8 w-8 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" />
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white border border-indigo-500 shadow-sm">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>

                <!-- Input Area -->
                <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <!-- Edit Indicator -->
                    <div id="editIndicator" class="hidden flex items-center justify-between bg-amber-900/30 border border-amber-500/30 rounded-lg p-3">
                        <span class="text-amber-200 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83zM3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"></path></svg>
                            Editing message...
                        </span>
                        <button type="button" onclick="cancelEdit()" class="text-amber-300 hover:text-amber-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" id="messageForm" class="space-y-2">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                        
                        <!-- File Upload & Emoji -->
                        <div class="flex gap-2 items-center flex-wrap">
                            <!-- File Attachment -->
                            <div class="relative group">
                                <input type="file" name="attachment" id="attachment" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx,.txt" onchange="handleFileSelect(this)">
                                <label for="attachment" class="p-3 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 cursor-pointer transition-colors inline-block rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span class="text-xs hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-gray-900 text-white px-2 py-1 rounded whitespace-nowrap">Attach file</span>
                                </label>
                                <div id="fileName" class="text-xs text-indigo-600 dark:text-indigo-400 hidden ml-2"></div>
                            </div>

                            <!-- Emoji Picker -->
                            <div x-data="{ showEmojis: false }" class="relative">
                                <button type="button" @click="showEmojis = !showEmojis" class="p-3 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors inline-block rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-lg">
                                    <span class="text-2xl">😊</span>
                                    <span class="text-xs hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-gray-900 text-white px-2 py-1 rounded whitespace-nowrap">Add emoji</span>
                                </button>
                                <div x-show="showEmojis" @click.outside="showEmojis = false" class="absolute bottom-full right-0 mb-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl p-2 grid grid-cols-4 gap-1 z-50">
                                    @foreach (['😊', '😂', '😍', '👍', '🎉', '🔥', '💯', '👏', '❤️', '✨', '🚀', '😭', '🤔', '😎', '🙌', '💪'] as $emoji)
                                        <button type="button" @click="insertEmoji('{{ $emoji }}')" class="text-2xl hover:bg-gray-200 dark:hover:bg-gray-700 p-1 rounded transition-colors">{{ $emoji }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="text-gray-400 text-xs ml-auto">
                                <span id="charCount">0</span> / 5000 characters
                            </div>
                        </div>

                        <!-- Textarea -->
                        <div class="flex gap-2 items-end">
                            <textarea 
                                id="messageInput"
                                name="body" 
                                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm resize-none py-2 px-3 text-sm"
                                placeholder="Type a message... (Max 5000 characters)"
                                oninput="updateCharCount(); autoResize(this); toggleSendButton();"
                                maxlength="5000"
                                rows="1"></textarea>
                            
                            <button type="submit" id="sendBtn" class="p-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600" disabled title="Ctrl+Enter or Cmd+Enter to send">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- File Preview -->
                        <div id="filePreview" class="hidden flex items-center justify-between bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-2">
                            <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <span id="previewName"></span>
                            </div>
                            <button type="button" onclick="clearFile()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Keyboard Help -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-right">Press Ctrl+Enter or Cmd+Enter to send</p>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentEditingId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Scroll to bottom on load
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }

            // Setup keyboard shortcut
            document.getElementById('messageInput').addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('messageForm').submit();
                }
            });
        });

        function updateCharCount() {
            const input = document.getElementById('messageInput');
            document.getElementById('charCount').textContent = input.value.length;
        }

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';
        }

        function toggleSendButton() {
            const input = document.getElementById('messageInput');
            const btn = document.getElementById('sendBtn');
            const hasText = input.value.trim().length > 0;
            const hasFile = document.getElementById('attachment').files.length > 0;
            btn.disabled = !(hasText || hasFile);
        }

        function handleFileSelect(input) {
            if (input.files.length > 0) {
                const file = input.files[0];
                const preview = document.getElementById('filePreview');
                const previewName = document.getElementById('previewName');
                previewName.textContent = file.name;
                preview.classList.remove('hidden');
                toggleSendButton();
            }
        }

        function clearFile() {
            document.getElementById('attachment').value = '';
            document.getElementById('filePreview').classList.add('hidden');
            toggleSendButton();
        }

        function insertEmoji(emoji) {
            const textarea = document.getElementById('messageInput');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            textarea.value = textarea.value.slice(0, start) + emoji + textarea.value.slice(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
            updateCharCount();
            toggleSendButton();
            autoResize(textarea);
        }

        async function editMessage(id, currentBody) {
            currentEditingId = id;
            const input = document.getElementById('messageInput');
            input.value = currentBody;
            document.getElementById('editIndicator').classList.remove('hidden');
            input.focus();
            updateCharCount();
            autoResize(input);
            toggleSendButton();
            
            // Change form action to update endpoint
            const form = document.getElementById('messageForm');
            const originalAction = form.action;
            form.action = `/messages/${id}`;
            form.method = 'POST';
            
            // Add method spoofing for PATCH
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);

            // Store original action to restore later
            form.dataset.originalAction = originalAction;
        }

        function cancelEdit() {
            document.getElementById('messageInput').value = '';
            document.getElementById('editIndicator').classList.add('hidden');
            currentEditingId = null;
            
            const form = document.getElementById('messageForm');
            form.action = form.dataset.originalAction;
            form.method = 'POST';
            
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
            
            updateCharCount();
            toggleSendButton();
        }

        async function deleteMessage(id) {
            if (!confirm('Delete this message? This cannot be undone.')) return;

            try {
                const response = await fetch(`/messages/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const messageEl = document.querySelector(`[data-message-id="${id}"]`);
                    if (messageEl) {
                        messageEl.style.opacity = '0.5';
                        messageEl.innerHTML = '<p class="text-gray-400 italic text-sm">Message deleted</p>';
                    }
                } else {
                    alert('Failed to delete message');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the message');
            }
        }
    </script>
    @endpush
</x-app-layout>

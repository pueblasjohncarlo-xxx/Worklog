<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <a href="{{ route('messages.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-3">
                    <img src="{{ $user->profile_photo_url }}" data-avatar-user-id="{{ $user->id }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-500">
                    <div>
                        <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                        <div class="text-xs text-indigo-600 dark:text-indigo-300 uppercase tracking-wider">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                    </div>
                </div>
            </h2>
        </div>
    </x-slot>

    <div class="py-6 min-h-[calc(100vh-160px)] bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-full">
                <!-- User Profile Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-lg sticky top-32 overflow-hidden border border-gray-200 dark:border-gray-700">
                        <!-- User Header -->
                        <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-600/20 dark:to-purple-600/20 border-b border-gray-200 dark:border-gray-700 text-center">
                            <img src="{{ $user->profile_photo_url }}" data-avatar-user-id="{{ $user->id }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover border-4 border-indigo-300 dark:border-indigo-500 shadow-md dark:shadow-lg mx-auto mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $user->name }}</h3>
                            <p class="text-xs text-indigo-600 dark:text-indigo-300 uppercase tracking-widest font-bold">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                        </div>

                        <!-- User Information -->
                        <div class="p-6 space-y-4">
                            <!-- Email -->
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-bold">Email</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 break-all">{{ $user->email }}</p>
                                </div>
                            </div>

                            <!-- Phone (if available) -->
                            @if($user->phone ?? null)
                                <div class="flex items-start gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-bold">Phone</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $user->phone }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Member Since -->
                            <div class="flex items-start gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-bold">Member Since</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="flex items-start gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                                <svg class="w-5 h-5 text-green-500 dark:text-green-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.5"></circle>
                                    <circle cx="12" cy="12" r="6" fill="currentColor"></circle>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-bold">Status</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">Active</p>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Messages count: <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $messages->count() }}</span></p>
                        </div>
                    </div>
                </div>


                <!-- Chat Area -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 h-full flex flex-col overflow-hidden shadow dark:shadow-lg rounded-lg border border-gray-200 dark:border-gray-700">
                
                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto flex flex-col gap-3 p-6 bg-white dark:bg-gray-900 scrollbar-thin scrollbar-thumb-indigo-300 dark:scrollbar-thumb-indigo-600 scrollbar-track-transparent" id="messages-container">
                    @if($messages->isEmpty())
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-indigo-300 dark:text-indigo-400/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-1">No messages yet</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Start by sending a message below!</p>
                            </div>
                        </div>
                    @else
                        @foreach($messages as $message)
                            @php
                                $isMe = $message->sender_id === Auth::id();
                            @endphp
                            <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }} items-end gap-2 group" data-message-id="{{ $message->id }}">
                                
                                @if(!$isMe)
                                    <div class="flex-shrink-0">
                                        <img class="h-8 w-8 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-500/50 shadow" src="{{ $message->sender->profile_photo_url }}" data-avatar-user-id="{{ $message->sender->id }}" alt="{{ $message->sender->name }}" title="{{ $message->sender->name }}" />
                                    </div>
                                @endif

                                <div class="max-w-[70%] sm:max-w-[60%] flex flex-col gap-1">
                                    <div class="{{ $isMe ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-r-2xl rounded-tl-2xl shadow-sm border border-gray-300 dark:border-gray-600' }} px-4 py-3 relative">
                                        @if($message->attachment_path)
                                            <div class="mb-2">
                                                @if($message->attachment_type === 'image')
                                                    <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="block">
                                                        <img src="{{ Storage::url($message->attachment_path) }}" alt="Attachment" class="rounded-lg max-w-full h-auto max-h-64 object-cover hover:opacity-90 transition-opacity shadow">
                                                    </a>
                                                @elseif($message->attachment_type === 'video')
                                                    <video controls class="rounded-lg max-w-full h-auto max-h-64 shadow">
                                                        <source src="{{ Storage::url($message->attachment_path) }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center gap-2 p-3 rounded-lg {{ $isMe ? 'bg-indigo-700 hover:bg-indigo-800' : 'bg-indigo-100 dark:bg-indigo-900/30 hover:bg-indigo-200 dark:hover:bg-indigo-800/40' }} transition-colors">
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
                                        
                                        <p class="text-[10px] mt-1 {{ $isMe ? 'text-indigo-200' : 'text-gray-600 dark:text-gray-400' }} text-right opacity-70 group-hover:opacity-100 transition-opacity">
                                            {{ $message->created_at->format('M d, h:i A') }}
                                            @if($message->is_edited) <span class="ml-1 font-semibold italic">(edited)</span> @endif
                                        </p>
                                        
                                        @if($message->read_at && $isMe)
                                            <p class="text-[9px] mt-0.5 {{ $isMe ? 'text-indigo-200' : 'text-indigo-600 dark:text-indigo-400' }} text-right flex items-center justify-end gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                Read
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Actions -->
                                @if($isMe)
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex flex-col gap-1">
                                        @if($message->canEdit(Auth::id()))
                                            <button type="button" onclick="editMessage({{ $message->id }}, '{{ addslashes($message->body) }}')" class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded transition-colors text-xs" title="Edit (5min window)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($message->canDelete(Auth::id()))
                                            <button type="button" onclick="deleteMessage({{ $message->id }})" class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors text-xs" title="Delete (60min window)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                @endif
                            
                                @if($isMe)
                                    <div class="flex-shrink-0">
                                        <img class="h-8 w-8 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-500/50 shadow" src="{{ Auth::user()->profile_photo_url }}" data-avatar-user-id="{{ Auth::id() }}" alt="{{ Auth::user()->name }}" />
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Input Area -->
                <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <!-- Edit Indicator -->
                    <div id="editIndicator" class="hidden flex items-center justify-between bg-amber-50 dark:bg-amber-900/40 border border-amber-300 dark:border-amber-500/40 rounded-lg p-3">
                        <span class="text-amber-800 dark:text-amber-200 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83zM3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"></path></svg>
                            Editing message...
                        </span>
                        <button type="button" onclick="cancelEdit()" class="text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-100 transition-colors">
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
                                <label for="attachment" class="p-2.5 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 cursor-pointer transition-colors inline-flex items-center justify-center rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </label>
                            </div>

                            <!-- Emoji Picker -->
                            <div x-data="{ showEmojis: false }" class="relative group">
                                <button type="button" @click="showEmojis = !showEmojis" class="p-2.5 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors inline-flex items-center justify-center rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 text-lg">
                                    <span class="text-xl">😊</span>
                                </button>
                                <div x-show="showEmojis" @click.outside="showEmojis = false" class="absolute bottom-full right-0 mb-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg dark:shadow-2xl p-2 grid grid-cols-4 gap-1 z-50">
                                    @foreach (['😊', '😂', '😍', '👍', '🎉', '🔥', '💯', '👏', '❤️', '✨', '🚀', '😭', '🤔', '😎', '🙌', '💪'] as $emoji)
                                        <button type="button" @click="insertEmoji('{{ $emoji }}')" class="text-2xl hover:bg-gray-100 dark:hover:bg-gray-800 p-1 rounded transition-colors">{{ $emoji }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="text-gray-500 dark:text-gray-400 text-xs ml-auto">
                                <span id="charCount">0</span> / 5000
                            </div>
                        </div>

                        <!-- Textarea -->
                        <div class="flex gap-2 items-end">
                            <textarea 
                                id="messageInput"
                                name="body" 
                                class="flex-1 rounded-2xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-1 shadow-sm resize-none py-3 px-4 text-sm transition-all"
                                placeholder="Aa"
                                oninput="updateCharCount(); autoResize(this); toggleSendButton();"
                                maxlength="5000"
                                rows="1"></textarea>
                            
                            <button type="submit" id="sendBtn" class="p-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600 flex-shrink-0" disabled title="Ctrl+Enter or Cmd+Enter to send">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- File Preview -->
                        <div id="filePreview" class="hidden flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-300 dark:border-indigo-500/30 rounded-lg p-3">
                            <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <span id="previewName" class="truncate"></span>
                            </div>
                            <button type="button" onclick="clearFile()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Keyboard Help -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-right italic">Press Ctrl+Enter or Cmd+Enter to send</p>
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
</x-app-layout>

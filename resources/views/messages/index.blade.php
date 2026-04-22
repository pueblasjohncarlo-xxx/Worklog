@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-120px)]" x-data="chatApp()" x-init="init(); return () => destroy()">
    <div class="grid grid-cols-1 lg:grid-cols-3 h-full gap-0 bg-gray-900/30">
        <!-- Left Panel: Conversations List -->
        <div class="lg:col-span-1 border-r border-indigo-500/20 flex flex-col bg-black/40 min-h-[400px] lg:min-h-auto">
            <!-- Header -->
            <div class="p-4 border-b border-indigo-500/20 bg-black/60 sticky top-0 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
                            <path d="M4 12a1 1 0 00-1 1v2a1 1 0 001 1h3a1 1 0 001-1v-2a1 1 0 00-1-1H4z"></path>
                        </svg>
                        Messages
                    </h2>
                    <button @click="openStartConversation()" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="hidden sm:inline">New Chat</span>
                    </button>
                </div>
                <div class="flex gap-2 mb-3">
                    <input x-model="searchQuery" @input="filterConversations()" 
                        type="text" placeholder="Search conversations..." 
                        class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button @click="roleFilter = ''" :class="roleFilter === '' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">All</button>
                    <button @click="roleFilter = 'student'" :class="roleFilter === 'student' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">Students</button>
                    <button @click="roleFilter = 'supervisor'" :class="roleFilter === 'supervisor' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">Supervisors</button>
                    <button @click="roleFilter = 'ojt_adviser'" :class="roleFilter === 'ojt_adviser' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">OJT Advisers</button>
                    <button @click="roleFilter = 'coordinator'" :class="roleFilter === 'coordinator' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">Coordinators</button>
                    <button @click="roleFilter = 'admin'" :class="roleFilter === 'admin' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">Admins</button>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                <template x-if="filteredConversations.length === 0">
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">No conversations yet</p>
                    </div>
                </template>

                <template x-for="conversation in filteredConversations" :key="conversation.id">
                    <button @click="selectConversation(conversation)"
                        :class="activeConversation?.id === conversation.id ? 'bg-indigo-600/30 border-l-2 border-indigo-500' : 'hover:bg-gray-800/50 border-l-2 border-transparent'"
                        class="w-full p-4 text-left border-b border-gray-800/50 transition hover:bg-gray-800/30">
                        
                        <div class="flex items-start gap-3">
                            <!-- Avatar -->
                            <div class="relative">
                                <img :src="conversation.avatar" :alt="conversation.name" :data-avatar-user-id="conversation.id"
                                    class="w-12 h-12 rounded-full flex-shrink-0 object-cover border border-indigo-500/30 transition-transform duration-200">
                                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-black" :class="isUserOnline(conversation.id) ? 'bg-emerald-400' : 'bg-gray-500'"></span>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-semibold text-white truncate"><span :data-user-name-id="conversation.id" x-text="conversation.name"></span></h3>
                                    <template x-if="conversation.unread_count > 0">
                                        <span class="bg-indigo-600 text-white text-xs rounded-full px-2 py-0.5 font-bold" 
                                            x-text="conversation.unread_count"></span>
                                    </template>
                                </div>
                                
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-xs text-indigo-400 capitalize" x-text="conversation.role"></span>
                                    <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_message_time)"></span>
                                </div>
                                
                                <p class="text-sm text-gray-400 truncate" x-text="isUserTyping(conversation.id) ? 'typing...' : (conversation.last_message || 'No messages yet')"></p>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Right Panel: Chat Window -->
        <div class="lg:col-span-2 flex flex-col bg-black/20">
            <template x-if="activeConversation === null">
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">Select a conversation</h3>
                        <p class="text-gray-500">Choose someone to start chatting</p>
                    </div>
                </div>
            </template>

            <template x-if="activeConversation !== null">
                <!-- Chat Header -->
                <div class="p-4 border-b border-indigo-500/20 bg-black/60 flex items-center justify-between sticky top-0 z-10">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img :src="activeConversation.avatar" :alt="activeConversation.name" :data-avatar-user-id="activeConversation.id"
                                class="w-10 h-10 rounded-full border border-indigo-500/30">
                            <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border border-black" :class="isUserOnline(activeConversation.id) ? 'bg-emerald-400' : 'bg-gray-500'"></span>
                        </div>
                        <div>
                            <h2 class="font-bold text-white" :data-user-name-id="activeConversation.id" x-text="activeConversation.name"></h2>
                            <p class="text-xs text-indigo-400 capitalize" x-text="isUserTyping(activeConversation.id) ? 'typing...' : (isUserOnline(activeConversation.id) ? 'online' : 'offline')"></p>
                        </div>
                    </div>
                    <button @click="activeConversation = null" class="p-2 hover:bg-gray-800 rounded-lg transition text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-4 py-2 border-b border-indigo-500/10 bg-black/30">
                    <input x-model="messageSearchQuery" type="text" placeholder="Search messages in this chat..." class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
                    <template x-if="messages.length === 0">
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500 text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                No messages yet. Start the conversation!
                            </p>
                        </div>
                    </template>

                    <template x-for="(group, date) in groupMessagesByDate()" :key="date">
                        <div class="flex justify-center">
                            <span class="text-xs text-gray-500 bg-black/40 px-3 py-1 rounded-full" x-text="date"></span>
                        </div>
                        
                        <template x-for="msg in group" :key="msg.id">
                            <div :class="msg.is_own ? 'justify-end' : 'justify-start'" class="flex">
                                <div :class="msg.is_own ? 'bg-indigo-600 text-white rounded-3xl rounded-tr-lg' : 'bg-gray-800 text-gray-100 rounded-3xl rounded-tl-lg'" 
                                    class="max-w-xs lg:max-w-md px-4 py-2 break-words transition-all duration-150">
                                    <!-- Message Body -->
                                    <template x-if="(msg.body || '').trim().length > 0">
                                        <p class="text-sm whitespace-pre-wrap" x-text="msg.body"></p>
                                    </template>
                                    
                                    <!-- Attachment Preview -->
                                    <template x-if="msg.attachment_path && msg.attachment_type === 'image'">
                                        <img :src="messageAttachmentUrl(msg)" :alt="msg.attachment_name" class="mt-2 rounded max-w-sm">
                                    </template>

                                    <template x-if="msg.attachment_path && msg.attachment_type === 'video'">
                                        <video controls class="mt-2 rounded max-w-sm w-full">
                                            <source :src="messageAttachmentUrl(msg)">
                                        </video>
                                    </template>
                                    
                                    <template x-if="msg.attachment_path && msg.attachment_type === 'file'">
                                        <a :href="messageAttachmentUrl(msg)" target="_blank" rel="noopener noreferrer"
                                            class="mt-2 text-xs underline inline-block hover:opacity-80">
                                            Attachment: <span x-text="msg.attachment_name || 'Attachment'"></span>
                                        </a>
                                    </template>

                                    <!-- Timestamp and Status -->
                                    <div class="flex items-center justify-between gap-2 mt-1">
                                        <span class="text-xs opacity-70" x-text="formatMessageTime(msg.created_at)"></span>
                                        <template x-if="msg.is_own">
                                            <span class="text-[10px] font-medium" x-text="messageDeliveryLabel(msg)"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>

                    <template x-if="activeConversation !== null && isUserTyping(activeConversation.id)">
                        <div class="flex justify-start" x-transition>
                            <div class="bg-gray-800 text-gray-300 rounded-2xl px-3 py-2 text-xs">typing...</div>
                        </div>
                    </template>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t border-indigo-500/20 bg-black/60">
                    <template x-if="selectedAttachment">
                        <div class="mb-3 rounded-lg border border-indigo-500/30 bg-indigo-900/20 p-3 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-indigo-200 truncate" x-text="selectedAttachmentName"></p>
                                <template x-if="selectedAttachmentPreview">
                                    <img :src="selectedAttachmentPreview" alt="Attachment preview" class="mt-2 max-h-28 rounded-md border border-indigo-500/30">
                                </template>
                            </div>
                            <button type="button" @click="clearAttachment()" class="text-xs px-2 py-1 rounded bg-gray-800 hover:bg-gray-700 text-gray-200 transition">Remove</button>
                        </div>
                    </template>

                    <form @submit.prevent="sendMessage()" class="flex gap-3">
                        <input type="file" x-ref="attachmentInput" class="hidden" @change="onAttachmentSelected($event)" accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                        <button type="button" @click="$refs.attachmentInput.click()" class="px-3 py-3 rounded-full bg-gray-800 hover:bg-gray-700 text-indigo-200 transition" title="Attach file">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l7.07-7.07a4 4 0 10-5.656-5.656l-7.778 7.778a6 6 0 108.486 8.486L21 12" />
                            </svg>
                        </button>
                        <input x-model="messageInput" type="text" placeholder="Type a message..." 
                            class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-full text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition text-sm"
                            @input="handleTypingInput()"
                            @blur="sendTypingState(false)">
                        <button type="submit" :disabled="!canSendMessage()" 
                            :class="canSendMessage() ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-700 opacity-50 cursor-not-allowed'"
                            class="px-6 py-3 rounded-full text-white font-semibold transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"></path>
                            </svg>
                            <span class="hidden sm:inline">Send</span>
                        </button>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <!-- Start Conversation Modal -->
    <div x-show="showStartConversationModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click="closeStartConversation()" x-transition>
        <div @click.stop class="bg-gray-900 rounded-lg shadow-2xl w-full max-w-md mx-auto border border-indigo-500/30 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-black/60 border-b border-indigo-500/20 p-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Start a Conversation</h3>
                <button @click="closeStartConversation()" class="p-1 hover:bg-gray-800 rounded transition text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Search Box -->
            <div class="p-4 border-b border-gray-800">
                <input x-model="startConversationSearch" @input="filterAvailableUsers()" 
                    type="text" placeholder="Search by name or email..." 
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <!-- Users List -->
            <div class="p-4 space-y-2">
                <template x-if="filteredAvailableUsers.length === 0">
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2s-3 1.343-3 3 1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm7 12c0-2.567-3.447-4-7-4s-7 1.433-7 4v2h14v-2z"></path>
                        </svg>
                        <p class="text-gray-400 text-sm">No users found</p>
                    </div>
                </template>

                <template x-for="user in filteredAvailableUsers" :key="user.id">
                    <div class="w-full p-3 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-indigo-500 transition flex items-center gap-3">
                        
                        <div class="flex items-center flex-1 min-w-0">
                            <!-- Avatar -->
                            <img :src="user.avatar" :alt="user.name" :data-avatar-user-id="user.id"
                                class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-indigo-500/30">
                            
                            <!-- User Info -->
                            <div class="flex-1 min-w-0 ml-3">
                                <h4 class="font-semibold text-white truncate" :data-user-name-id="user.id" x-text="user.name"></h4>
                                <p class="text-xs text-gray-400 truncate" :data-user-email-id="user.id" x-text="user.email"></p>
                                <span class="text-xs text-indigo-400 capitalize" x-text="user.role"></span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2 flex-shrink-0">
                            <!-- Chat Button -->
                            <button @click="startConversationWithUser(user)"
                                class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" title="Chat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </button>
                            
                            <!-- Gmail Button -->
                            <a :href="`https://mail.google.com/mail/?view=cm&fs=1&to=${user.email}`" target="_blank" rel="noopener noreferrer"
                                class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition" title="Send Email">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function chatApp() {
    return {
        conversations: [],
        filteredConversations: [],
        activeConversation: null,
        messages: [],
        searchQuery: '',
        roleFilter: '',
        messageInput: '',
        isLoading: false,
        pollInterval: null,
        heartbeatInterval: null,
        relativeTimeInterval: null,
        showStartConversationModal: false,
        availableUsers: [],
        filteredAvailableUsers: [],
        startConversationSearch: '',
        searchTimeout: null,
        typingStopTimer: null,
        onlineState: {},
        typingState: {},
        relativeTimeTick: 0,
        messageSearchQuery: '',
        selectedAttachment: null,
        selectedAttachmentPreview: '',
        selectedAttachmentName: '',

        async init() {
            await this.loadConversations();

            // Deep-link support: /messages?open={userId}
            try {
                const openIdRaw = new URLSearchParams(window.location.search).get('open');
                const openId = openIdRaw ? parseInt(openIdRaw, 10) : null;
                if (openId && Number.isFinite(openId)) {
                    const match = this.conversations.find((c) => Number(c.id) === openId);
                    if (match) {
                        await this.selectConversation(match);
                    } else {
                        // If not in list (rare), try to open directly.
                        await this.startConversationWithUser({ id: openId, name: 'Conversation', email: '', role: '', avatar: '' });
                    }
                }
            } catch (e) {
                // no-op
            }
            this.sendPresenceHeartbeat();

            // Poll for real-time updates
            this.pollInterval = setInterval(() => {
                this.performRealtimeSync();
            }, 1200);

            this.heartbeatInterval = setInterval(() => {
                this.sendPresenceHeartbeat();
            }, 15000);

            this.relativeTimeInterval = setInterval(() => {
                this.relativeTimeTick += 1;
            }, 30000);
        },

        async performRealtimeSync() {
            await this.loadConversations();

            if (this.activeConversation) {
                await this.loadMessages();
                await this.loadTypingStatus(this.activeConversation.id);
            }

            this.sendPresenceHeartbeat();
        },

        async loadConversations() {
            try {
                const response = await fetch('/api/messages/conversations?include_contacts=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });
                const data = await response.json();
                if (data.success) {
                    this.conversations = data.conversations;
                    this.filterConversations();
                    this.loadPresenceForConversations();
                    this.loadTypingForConversations();
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            }
        },

        async loadPresenceForConversations() {
            const ids = this.conversations.map((conversation) => conversation.id);
            if (ids.length === 0) {
                return;
            }

            try {
                const params = new URLSearchParams();
                params.set('ids', ids.join(','));

                const response = await fetch(`/api/messages/presence?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });
                const data = await response.json();
                if (data.success && data.presence) {
                    this.onlineState = {
                        ...this.onlineState,
                        ...data.presence,
                    };
                }
            } catch (error) {
                console.error('Error loading presence:', error);
            }
        },

        async sendPresenceHeartbeat() {
            try {
                await fetch('/api/messages/presence/heartbeat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                    body: JSON.stringify({
                        active_conversation_id: this.activeConversation ? this.activeConversation.id : null,
                    }),
                });
            } catch (error) {
                console.error('Error sending heartbeat:', error);
            }
        },

        async loadTypingForConversations() {
            const ids = this.conversations.map((conversation) => conversation.id);
            if (ids.length === 0) {
                return;
            }

            try {
                const params = new URLSearchParams();
                params.set('ids', ids.join(','));

                const response = await fetch(`/api/messages/typing?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });
                const data = await response.json();

                if (data.success && data.typing) {
                    this.typingState = {
                        ...this.typingState,
                        ...data.typing,
                    };
                }
            } catch (error) {
                console.error('Error loading typing states:', error);
            }
        },

        isUserOnline(userId) {
            const state = this.onlineState[String(userId)];
            return !!(state && state.online);
        },

        isUserTyping(userId) {
            return !!this.typingState[String(userId)];
        },

        filterConversations() {
            this.filteredConversations = this.conversations.filter(conv => {
                const matchesSearch = !this.searchQuery || 
                    conv.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    conv.email.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                const matchesRole = !this.roleFilter || conv.role === this.roleFilter;
                
                return matchesSearch && matchesRole;
            }).sort((a, b) => {
                const aTime = a.last_message_time ? new Date(a.last_message_time).getTime() : 0;
                const bTime = b.last_message_time ? new Date(b.last_message_time).getTime() : 0;
                return bTime - aTime;
            });
        },

        async selectConversation(conversation) {
            this.activeConversation = conversation;
            this.messageInput = '';
            this.messages = [];
            this.messageSearchQuery = '';
            await this.loadMessages();
            await this.loadTypingStatus(conversation.id);
            this.sendPresenceHeartbeat();
            this.scrollToBottom();
        },

        async loadMessages() {
            if (!this.activeConversation) return;
            try {
                const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                const url = new URL(`/api/messages/conversation/${this.activeConversation.id}`, window.location.origin);
                if (lastId && Number.isFinite(Number(lastId)) && String(lastId).startsWith('temp-') === false) {
                    url.searchParams.set('after_id', String(lastId));
                }

                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });
                const data = await response.json();
                if (data.success) {
                    const newMessages = data.messages || [];

                    if (lastId && String(lastId).startsWith('temp-')) {
                        // If we have optimistic messages, do a full reload once to reconcile.
                        const fullResponse = await fetch(`/api/messages/conversation/${this.activeConversation.id}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            cache: 'no-store',
                        });
                        const fullData = await fullResponse.json();
                        if (fullData.success) {
                            this.messages = fullData.messages || [];
                        }
                    } else if (lastId > 0) {
                        if (newMessages.length > 0) {
                            this.messages = [...this.messages, ...newMessages];
                        }
                    } else {
                        this.messages = newMessages;
                    }

                    if (data.user && this.activeConversation) {
                        this.activeConversation.avatar = data.user.avatar;
                        this.activeConversation.name = data.user.name;
                        this.activeConversation.email = data.user.email;
                        this.activeConversation.role = data.user.role;
                        this.conversations = this.conversations.map((conversation) => {
                            if (conversation.id !== data.user.id) {
                                return conversation;
                            }

                            return {
                                ...conversation,
                                avatar: data.user.avatar,
                                name: data.user.name,
                                email: data.user.email,
                                role: data.user.role,
                            };
                        });
                        this.filterConversations();
                    }
                    
                    if (newMessages.length > 0) {
                        setTimeout(() => this.scrollToBottom(), 100);
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        async sendMessage() {
            if (!this.canSendMessage() || !this.activeConversation) return;

            const message = this.messageInput;
            const attachment = this.selectedAttachment;
            this.messageInput = '';
            this.isLoading = true;

            const tempId = `temp-${Date.now()}`;
            const optimisticMessage = {
                id: tempId,
                sender_id: null,
                receiver_id: this.activeConversation.id,
                body: message,
                read_at: null,
                created_at: new Date().toISOString(),
                attachment_path: null,
                attachment_type: null,
                attachment_name: attachment ? attachment.name : null,
                is_own: true,
                sender_name: 'You',
            };

            if (attachment) {
                optimisticMessage.attachment_type = attachment.type && attachment.type.startsWith('image/')
                    ? 'image'
                    : (attachment.type && attachment.type.startsWith('video/') ? 'video' : 'file');
                optimisticMessage.attachment_path = optimisticMessage.attachment_type === 'image' && this.selectedAttachmentPreview
                    ? this.selectedAttachmentPreview
                    : null;
            }

            this.messages.push(optimisticMessage);
            this.scrollToBottom();

            try {
                const formData = new FormData();
                formData.append('receiver_id', this.activeConversation.id);
                formData.append('body', message);
                if (attachment) {
                    formData.append('attachment', attachment);
                }

                const response = await fetch('/api/messages/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    const tempIndex = this.messages.findIndex((msg) => msg.id === tempId);
                    if (tempIndex !== -1) {
                        this.messages.splice(tempIndex, 1, data.message);
                    } else {
                        this.messages.push(data.message);
                    }
                    this.sendTypingState(false);
                    this.clearAttachment();
                    this.scrollToBottom();
                    await this.loadConversations();
                } else if (data.error) {
                    throw new Error(data.error);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.messageInput = message; // Restore message on error
                this.messages = this.messages.filter((msg) => msg.id !== tempId);
                if (attachment) {
                    this.selectedAttachment = attachment;
                }
            } finally {
                this.isLoading = false;
            }
        },

        canSendMessage() {
            return !!(this.activeConversation && (this.messageInput.trim().length > 0 || this.selectedAttachment));
        },

        onAttachmentSelected(event) {
            const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
            if (!file) {
                this.clearAttachment();
                return;
            }

            this.selectedAttachment = file;
            this.selectedAttachmentName = file.name;
            this.selectedAttachmentPreview = '';

            if (file.type && file.type.startsWith('image/')) {
                this.selectedAttachmentPreview = URL.createObjectURL(file);
            }
        },

        clearAttachment() {
            if (this.selectedAttachmentPreview) {
                URL.revokeObjectURL(this.selectedAttachmentPreview);
            }

            this.selectedAttachment = null;
            this.selectedAttachmentPreview = '';
            this.selectedAttachmentName = '';

            if (this.$refs && this.$refs.attachmentInput) {
                this.$refs.attachmentInput.value = '';
            }
        },

        groupMessagesByDate() {
            const groups = {};
            this.filteredMessages().forEach(msg => {
                const date = this.formatDate(msg.created_at);
                if (!groups[date]) groups[date] = [];
                groups[date].push(msg);
            });
            return groups;
        },

        filteredMessages() {
            const query = this.messageSearchQuery.trim().toLowerCase();
            if (!query) {
                return this.messages;
            }

            return this.messages.filter((msg) => {
                const bodyMatch = (msg.body || '').toLowerCase().includes(query);
                const attachmentNameMatch = (msg.attachment_name || '').toLowerCase().includes(query);
                return bodyMatch || attachmentNameMatch;
            });
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            if (date.toDateString() === today.toDateString()) {
                return 'Today';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined });
            }
        },

        formatTime(dateString) {
            this.relativeTimeTick;
            if (!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffMins < 1) return 'now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        },

        formatMessageTime(dateString) {
            this.relativeTimeTick;
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        },

        messageDeliveryLabel(msg) {
            if (msg.read_at) {
                return 'Seen';
            }

            if (msg.id && String(msg.id).startsWith('temp-')) {
                return 'Sending...';
            }

            return 'Delivered';
        },

        messageAttachmentUrl(msg) {
            if (!msg || !msg.attachment_path) {
                return '';
            }

            if (msg.attachment_url) {
                return msg.attachment_url;
            }

            if (msg.attachment_path.startsWith('blob:') || msg.attachment_path.startsWith('http://') || msg.attachment_path.startsWith('https://')) {
                return msg.attachment_path;
            }

            return `/storage/${msg.attachment_path}`;
        },

        async loadTypingStatus(userId) {
            try {
                const response = await fetch(`/api/messages/typing/${userId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });
                const data = await response.json();

                if (data.success) {
                    this.typingState = {
                        ...this.typingState,
                        [String(userId)]: !!data.typing,
                    };
                }
            } catch (error) {
                console.error('Error loading typing status:', error);
            }
        },

        async sendTypingState(typing) {
            if (!this.activeConversation) {
                return;
            }

            try {
                await fetch('/api/messages/typing', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                    body: JSON.stringify({
                        receiver_id: this.activeConversation.id,
                        typing: !!typing,
                    }),
                });
            } catch (error) {
                console.error('Error updating typing state:', error);
            }
        },

        handleTypingInput() {
            if (!this.activeConversation) {
                return;
            }

            const hasText = this.messageInput.trim().length > 0;
            this.sendTypingState(hasText);

            if (this.typingStopTimer) {
                clearTimeout(this.typingStopTimer);
            }

            this.typingStopTimer = setTimeout(() => {
                this.sendTypingState(false);
            }, 1200);
        },

        scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 50);
            }
        },

        async openStartConversation() {
            this.showStartConversationModal = true;
            this.startConversationSearch = '';
            this.availableUsers = [];
            this.filteredAvailableUsers = [];
            await this.loadAvailableUsers();
        },

        closeStartConversation() {
            this.showStartConversationModal = false;
            this.startConversationSearch = '';
            this.filteredAvailableUsers = [];
        },

        async loadAvailableUsers() {
            try {
                const url = new URL('/api/messages/available-users', window.location.origin);
                if (this.startConversationSearch && this.startConversationSearch.trim()) {
                    url.searchParams.append('search', this.startConversationSearch.trim());
                }

                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success === false) {
                    throw new Error(data.error || 'API returned success:false');
                }
                
                if (!data.users) {
                    throw new Error('No users field in response');
                }

                this.availableUsers = data.users || [];
                this.filteredAvailableUsers = this.availableUsers;
                
                
            } catch (error) {
                console.error('Error loading available users:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack,
                });
                this.availableUsers = [];
                this.filteredAvailableUsers = [];
            }
        },

        filterAvailableUsers() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadAvailableUsers();
            }, 300);
        },

        async startConversationWithUser(user) {
            try {
                const existingConversation = this.conversations.find(c => c.id === user.id);

                if (existingConversation) {
                    await this.selectConversation(existingConversation);
                } else {
                    this.activeConversation = {
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        role: user.role,
                        avatar: user.avatar,
                        unread_count: 0,
                        last_message: null,
                        last_message_time: null
                    };
                    this.messageInput = '';
                    this.messages = [];
                    await this.loadMessages();
                    this.scrollToBottom();
                }

                this.closeStartConversation();
            } catch (error) {
                console.error('Error starting conversation:', error);
            }
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
            if (this.heartbeatInterval) {
                clearInterval(this.heartbeatInterval);
            }
            if (this.relativeTimeInterval) {
                clearInterval(this.relativeTimeInterval);
            }
            if (this.typingStopTimer) {
                clearTimeout(this.typingStopTimer);
            }
            this.sendTypingState(false);
        }
    };
}
</script>
@endsection

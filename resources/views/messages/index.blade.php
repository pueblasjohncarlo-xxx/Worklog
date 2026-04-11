@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-120px)]" x-data="chatApp()" @load="init()" @destroy="destroy()">
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
                    <button @click="roleFilter = 'coordinator'" :class="roleFilter === 'coordinator' ? 'bg-indigo-600' : 'bg-gray-800 hover:bg-gray-700'" 
                        class="px-3 py-1 rounded text-xs text-white transition">Coordinators</button>
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
                            <img :src="conversation.avatar" :alt="conversation.name" 
                                class="w-12 h-12 rounded-full flex-shrink-0 object-cover border border-indigo-500/30">
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-semibold text-white truncate"><span x-text="conversation.name"></span></h3>
                                    <template x-if="conversation.unread_count > 0">
                                        <span class="bg-indigo-600 text-white text-xs rounded-full px-2 py-0.5 font-bold" 
                                            x-text="conversation.unread_count"></span>
                                    </template>
                                </div>
                                
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-xs text-indigo-400 capitalize" x-text="conversation.role"></span>
                                    <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_message_time)"></span>
                                </div>
                                
                                <p class="text-sm text-gray-400 truncate" x-text="conversation.last_message || 'No messages yet'"></p>
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
                        <img :src="activeConversation.avatar" :alt="activeConversation.name" 
                            class="w-10 h-10 rounded-full border border-indigo-500/30">
                        <div>
                            <h2 class="font-bold text-white" x-text="activeConversation.name"></h2>
                            <p class="text-xs text-indigo-400 capitalize" x-text="activeConversation.role"></p>
                        </div>
                    </div>
                    <button @click="activeConversation = null" class="p-2 hover:bg-gray-800 rounded-lg transition text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
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
                                    class="max-w-xs lg:max-w-md px-4 py-2 break-words">
                                    <!-- Message Body -->
                                    <p class="text-sm" x-text="msg.body"></p>
                                    
                                    <!-- Attachment Preview -->
                                    <template x-if="msg.attachment_path && msg.attachment_type === 'image'">
                                        <img :src="`/storage/${msg.attachment_path}`" :alt="msg.attachment_name" 
                                            class="mt-2 rounded max-w-sm">
                                    </template>
                                    
                                    <template x-if="msg.attachment_path && msg.attachment_type === 'file'">
                                        <a :href="`/storage/${msg.attachment_path}`" download 
                                            class="mt-2 text-xs underline inline-block hover:opacity-80">
                                            📎 <span x-text="msg.attachment_name"></span>
                                        </a>
                                    </template>

                                    <!-- Timestamp and Status -->
                                    <div class="flex items-center justify-between gap-2 mt-1">
                                        <span class="text-xs opacity-70" x-text="formatMessageTime(msg.created_at)"></span>
                                        <template x-if="msg.is_own">
                                            <template x-if="msg.read_at">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </template>
                                            <template x-if="!msg.read_at">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t border-indigo-500/20 bg-black/60">
                    <form @submit.prevent="sendMessage()" class="flex gap-3">
                        <input x-model="messageInput" type="text" placeholder="Type a message..." 
                            class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-full text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition text-sm"
                            @keydown.enter="sendMessage()">
                        <button type="submit" :disabled="!messageInput.trim()" 
                            :class="messageInput.trim() ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-700 opacity-50 cursor-not-allowed'"
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
                            <img :src="user.avatar" :alt="user.name" 
                                class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-indigo-500/30">
                            
                            <!-- User Info -->
                            <div class="flex-1 min-w-0 ml-3">
                                <h4 class="font-semibold text-white truncate" x-text="user.name"></h4>
                                <p class="text-xs text-gray-400 truncate" x-text="user.email"></p>
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
        showStartConversationModal: false,
        availableUsers: [],
        filteredAvailableUsers: [],
        startConversationSearch: '',
        searchTimeout: null,

        init() {
            console.log('=== CHATAPP INITIALIZED ===');
            console.log('Component state:', {
                conversations: this.conversations.length,
                availableUsers: this.availableUsers.length,
                filteredAvailableUsers: this.filteredAvailableUsers.length,
                showStartConversationModal: this.showStartConversationModal,
                activeConversation: this.activeConversation,
            });
            this.loadConversations();
            // Poll for new messages every 2 seconds
            this.pollInterval = setInterval(() => {
                if (this.activeConversation) {
                    this.loadMessages();
                }
                this.loadConversations();
            }, 2000);
            console.log('=== INITIALIZATION COMPLETE ===');
        },

        async loadConversations() {
            try {
                const response = await fetch('/api/messages/conversations');
                const data = await response.json();
                if (data.success) {
                    this.conversations = data.conversations;
                    this.filterConversations();
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            }
        },

        filterConversations() {
            this.filteredConversations = this.conversations.filter(conv => {
                const matchesSearch = !this.searchQuery || 
                    conv.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    conv.email.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                const matchesRole = !this.roleFilter || conv.role === this.roleFilter;
                
                return matchesSearch && matchesRole;
            }).sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time));
        },

        async selectConversation(conversation) {
            this.activeConversation = conversation;
            this.messageInput = '';
            this.messages = [];
            await this.loadMessages();
            this.scrollToBottom();
        },

        async loadMessages() {
            if (!this.activeConversation) return;
            try {
                const response = await fetch(`/api/messages/conversation/${this.activeConversation.id}`);
                const data = await response.json();
                if (data.success) {
                    // Check if there are new messages by comparing last message ID
                    const lastMsgId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : null;
                    this.messages = data.messages;
                    
                    if (lastMsgId === null || this.messages.some(m => m.id > lastMsgId)) {
                        setTimeout(() => this.scrollToBottom(), 100);
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        async sendMessage() {
            if (!this.messageInput.trim() || !this.activeConversation) return;

            const message = this.messageInput;
            this.messageInput = '';
            this.isLoading = true;

            try {
                const response = await fetch('/api/messages/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        receiver_id: this.activeConversation.id,
                        body: message,
                    }),
                });

                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.scrollToBottom();
                    await this.loadConversations();
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.messageInput = message; // Restore message on error
            } finally {
                this.isLoading = false;
            }
        },

        groupMessagesByDate() {
            const groups = {};
            this.messages.forEach(msg => {
                const date = this.formatDate(msg.created_at);
                if (!groups[date]) groups[date] = [];
                groups[date].push(msg);
            });
            return groups;
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
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
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
            console.log('=== OPENING START CONVERSATION MODAL ===');
            this.showStartConversationModal = true;
            this.startConversationSearch = '';
            this.availableUsers = [];
            this.filteredAvailableUsers = [];
            console.log('Modal state:', {
                showing: this.showStartConversationModal,
                search: this.startConversationSearch,
                users: this.availableUsers,
                filtered: this.filteredAvailableUsers,
            });
            console.log('Calling loadAvailableUsers()...');
            // Load users immediately when modal opens
            await this.loadAvailableUsers();
            console.log('=== MODAL OPEN COMPLETE ===');
        },

        closeStartConversation() {
            this.showStartConversationModal = false;
            this.startConversationSearch = '';
            this.filteredAvailableUsers = [];
        },

        async loadAvailableUsers() {
            try {
                console.log('=== LOADING AVAILABLE USERS ===');
                console.log('Search term:', this.startConversationSearch);
                
                // Build query string with search param if provided
                const url = new URL('/api/messages/available-users', window.location.origin);
                if (this.startConversationSearch && this.startConversationSearch.trim()) {
                    url.searchParams.append('search', this.startConversationSearch.trim());
                    console.log('Added search param to URL');
                }
                
                console.log('Fetching from:', url.toString());
                const response = await fetch(url.toString());
                
                console.log('Response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    headers: {
                        contentType: response.headers.get('content-type'),
                    },
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                console.log('Response data:', data);
                
                if (data.success === false) {
                    throw new Error(data.error || 'API returned success:false');
                }
                
                if (!data.users) {
                    throw new Error('No users field in response');
                }

                this.availableUsers = data.users || [];
                this.filteredAvailableUsers = this.availableUsers;
                
                console.log('Data assigned:', {
                    availableUsersCount: this.availableUsers.length,
                    filteredAvailableUsersCount: this.filteredAvailableUsers.length,
                });
                
                console.log(`✓ Successfully loaded ${this.availableUsers.length} users`);
                console.log('=== LOADING COMPLETE ===');
            } catch (error) {
                console.error('✗ Error loading available users:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack,
                });
                this.availableUsers = [];
                this.filteredAvailableUsers = [];
            }
        },

        filterAvailableUsers() {
            console.log('filterAvailableUsers called with search:', this.startConversationSearch);
            // Debounce search to avoid excessive API calls
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                console.log('Search debounce complete, calling loadAvailableUsers()');
                this.loadAvailableUsers();
            }, 300); // Wait 300ms after user stops typing
        },

        async startConversationWithUser(user) {
            try {
                console.log('Starting conversation with user:', user);
                
                // Check if we already have a conversation with this user
                const existingConversation = this.conversations.find(c => c.id === user.id);
                
                if (existingConversation) {
                    console.log('Opening existing conversation');
                    // Open existing conversation
                    this.selectConversation(existingConversation);
                } else {
                    console.log('Creating new conversation');
                    // Start new conversation by selecting the user
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
                alert('Unable to start conversation. Please try again.');
            }
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
        }
    };
}
</script>
@endsection

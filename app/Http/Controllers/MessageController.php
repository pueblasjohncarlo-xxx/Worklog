<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MessageController extends Controller
{
    private const ONLINE_TTL_SECONDS = 45;

    private const TYPING_TTL_SECONDS = 6;

    public function index(Request $request): View
    {
        $userId = Auth::id();
        $q = trim((string) $request->input('q', ''));

        // Get all unique users that the current user has exchanged messages with
        $sentTo = Message::where('sender_id', $userId)->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', $userId)->pluck('sender_id');

        $contactIds = $sentTo->merge($receivedFrom)->unique();

        $contacts = User::whereIn('id', $contactIds)->get()->map(function ($contact) use ($userId) {
            $lastMessage = Message::where(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $userId)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $userId);
            })->latest()->first();

            $contact->last_message = $lastMessage;
            $contact->unread_count = Message::where('sender_id', $contact->id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            return $contact;
        })->sortByDesc('last_message.created_at');

        if ($q !== '') {
            $contacts = $contacts->filter(function ($contact) use ($q) {
                $nameMatch = stripos($contact->name, $q) === 0 || stripos($contact->name, $q) !== false;
                $bodyMatch = $contact->last_message && stripos((string) $contact->last_message->body, $q) !== false;

                return $nameMatch || $bodyMatch;
            });
        }

        return view('messages.index', [
            'contacts' => $contacts,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        $user = Auth::user();
        $potentialRecipients = $this->getPotentialRecipients($user);

        // If user has no potential recipients, provide all users based on role
        if ($potentialRecipients->isEmpty()) {
            $potentialRecipients = match ($user->role) {
                User::ROLE_STUDENT => User::where('role', User::ROLE_COORDINATOR)->orderBy('name')->get(),
                User::ROLE_COORDINATOR => User::whereIn('role', [User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER])->orderBy('name')->get(),
                User::ROLE_SUPERVISOR => User::where('role', User::ROLE_COORDINATOR)->orderBy('name')->get(),
                User::ROLE_ADMIN, User::ROLE_OJT_ADVISER => User::where('id', '!=', $user->id)->orderBy('name')->get(),
                default => collect(),
            };
        }

        return view('messages.create', compact('potentialRecipients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id|not_in:' . Auth::id(),
            'body' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if (!$request->body && !$request->hasFile('attachment')) {
            return back()->withErrors(['body' => 'Message body or attachment is required.']);
        }

        if (!$this->canMessageUser($request->receiver_id)) {
            return back()->withErrors(['receiver_id' => 'You cannot message this user.']);
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('message_attachments', 'public');
            $attachmentName = $file->getClientOriginalName();

            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $attachmentType = 'video';
            } else {
                $attachmentType = 'file';
            }
        }

        $body = $request->body ?? '';

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => $body,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
        ]);

        $recipient = User::find($request->receiver_id);
        if ($recipient) {
            $recipient->notify(new NewMessageNotification($message));
        }

        return redirect()->route('messages.show', $request->receiver_id);
    }

    public function show(User $user): View
    {
        $authId = Auth::id();

        if (!$this->canViewConversation($user->id)) {
            abort(403, 'Unauthorized to view this conversation');
        }

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($q) use ($authId, $user) {
            $q->where('sender_id', $authId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $authId);
        })->orderBy('created_at', 'asc')->get();

        return view('messages.show', compact('user', 'messages'));
    }

    public function update(Message $message, Request $request): JsonResponse
    {
        if (!$message->canEdit(Auth::id())) {
            return response()->json(['error' => 'Cannot edit this message'], 403);
        }

        $request->validate(['body' => 'required|string|max:5000']);

        $message->update([
            'body' => $request->body,
            'is_edited' => true,
            'edited_by' => Auth::id(),
        ]);

        return response()->json($message);
    }

    public function delete(Message $message): JsonResponse
    {
        if (!$message->canDelete(Auth::id())) {
            return response()->json(['error' => 'Cannot delete this message'], 403);
        }

        $message->delete();
        return response()->json(['success' => true]);
    }

    public function markAsRead(Message $message): JsonResponse
    {
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();
        return response()->json(['success' => true]);
    }

    private function canMessageUser($receiverId): bool
    {
        $user = Auth::user();
        $receiverUser = User::find($receiverId);

        if (!$receiverUser || $receiverId === $user->id) {
            return false;
        }

        return match ($user->role) {
            User::ROLE_STUDENT => $this->studentCanMessage($receiverUser),
            User::ROLE_COORDINATOR => $this->coordinatorCanMessage($receiverUser),
            User::ROLE_SUPERVISOR => $this->supervisorCanMessage($receiverUser),
            User::ROLE_ADMIN, User::ROLE_OJT_ADVISER => true,
            default => false,
        };
    }

    private function canViewConversation($userId): bool
    {
        return $this->canMessageUser($userId);
    }

    private function studentCanMessage(User $user): bool
    {
        return $user->role === User::ROLE_COORDINATOR ||
            Assignment::where('student_id', Auth::id())
                ->where('supervisor_id', $user->id)
                ->exists();
    }

    private function coordinatorCanMessage(User $user): bool
    {
        return $user->role === User::ROLE_SUPERVISOR ||
            Assignment::where('student_id', $user->id)->exists();
    }

    private function supervisorCanMessage(User $user): bool
    {
        return $user->role === User::ROLE_COORDINATOR ||
            Assignment::where('supervisor_id', Auth::id())
                ->where('student_id', $user->id)
                ->exists();
    }

    private function getPotentialRecipients($user)
    {
        if ($user->role === User::ROLE_SUPERVISOR) {
            // Supervisors can message: coordinators + students assigned to them
            $coordinators = User::where('role', User::ROLE_COORDINATOR)->orderBy('name')->get();
            $studentIds = Assignment::where('supervisor_id', $user->id)->pluck('student_id');
            $students = User::whereIn('id', $studentIds)->orderBy('name')->get();
            return $coordinators->merge($students)->unique('id')->sortBy('name');
            
        } elseif ($user->role === User::ROLE_COORDINATOR) {
            // Coordinators can message: supervisors + students (any user with student role)
            $supervisors = User::where('role', User::ROLE_SUPERVISOR)->orderBy('name')->get();
            $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();
            return $supervisors->merge($students)->unique('id')->sortBy('name');
                
        } elseif ($user->role === User::ROLE_STUDENT) {
            // Students can message: their supervisors + coordinators
            $supervisorIds = Assignment::where('student_id', $user->id)->pluck('supervisor_id');
            $supervisors = User::whereIn('id', $supervisorIds)->orderBy('name')->get();
            
            // Also get coordinators as a fallback
            $coordinators = User::where('role', User::ROLE_COORDINATOR)->orderBy('name')->get();
            
            return $supervisors->merge($coordinators)->unique('id')->sortBy('name');
            
        } elseif ($user->role === User::ROLE_OJT_ADVISER) {
            // OJT Advisers can message everyone except themselves
            return User::where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
                
        } else {
            // Admin and others can message everyone
            return User::where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }
    }

    // ==================== API Methods for Real-time Updates ====================
    
    public function apiConversations(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $q = trim((string) $request->input('q', ''));
        $roleFilter = $request->input('role', '');

        // Get all unique users that the current user has exchanged messages with
        $sentTo = Message::where('sender_id', $userId)->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', $userId)->pluck('sender_id');

        $contactIds = $sentTo->merge($receivedFrom)->unique();

        $contacts = User::whereIn('id', $contactIds)->get()->map(function ($contact) use ($userId) {
            $lastMessage = Message::where(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $userId)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $userId);
            })->latest()->first();

            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'role' => $contact->role,
                'avatar' => $contact->profile_photo_url,
                'last_message' => $lastMessage ? $lastMessage->body : null,
                'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                'unread_count' => Message::where('sender_id', $contact->id)
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count(),
            ];
        })->sortByDesc('last_message_time');

        // Apply filters
        if ($q !== '') {
            $contacts = $contacts->filter(function ($contact) use ($q) {
                return stripos($contact['name'], $q) !== false || stripos($contact['email'], $q) !== false;
            });
        }

        if ($roleFilter !== '') {
            $contacts = $contacts->filter(function ($contact) use ($roleFilter) {
                return $contact['role'] === $roleFilter;
            });
        }

        return response()->json([
            'success' => true,
            'conversations' => $contacts->values(),
            'total_unread' => Message::where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function apiConversation(User $user, Request $request): JsonResponse
    {
        $authId = Auth::id();

        if (!$this->canViewConversation($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark received messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($q) use ($authId, $user) {
            $q->where('sender_id', $authId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $authId);
        })->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($authId) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'body' => $msg->body,
                    'read_at' => $msg->read_at,
                    'is_edited' => $msg->is_edited,
                    'created_at' => $msg->created_at,
                    'attachment_path' => $msg->attachment_path,
                    'attachment_type' => $msg->attachment_type,
                    'attachment_name' => $msg->attachment_name,
                    'is_own' => $msg->sender_id === $authId,
                    'sender_name' => $msg->sender->name,
                ];
            });

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->profile_photo_url,
            ],
            'messages' => $messages,
        ]);
    }

    public function apiSend(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id|not_in:' . Auth::id(),
            'body' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if (! $request->filled('body') && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Message body or attachment is required.'], 422);
        }

        if (!$this->canMessageUser($request->receiver_id)) {
            return response()->json(['error' => 'You cannot message this user'], 403);
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('message_attachments', 'public');
            $attachmentName = $file->getClientOriginalName();

            $mimeType = $file->getMimeType() ?? '';
            if (str_starts_with($mimeType, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $attachmentType = 'video';
            } else {
                $attachmentType = 'file';
            }
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => (string) ($request->input('body') ?? ''),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
        ]);

        $recipient = User::find($request->receiver_id);
        if ($recipient) {
            $recipient->notify(new NewMessageNotification($message));
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'body' => $message->body,
                'read_at' => $message->read_at,
                'created_at' => $message->created_at,
                'attachment_path' => $message->attachment_path,
                'attachment_type' => $message->attachment_type,
                'attachment_name' => $message->attachment_name,
                'is_own' => true,
                'sender_name' => Auth::user()->name,
            ],
        ]);
    }

    public function apiMarkAsRead(Message $message): JsonResponse
    {
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();
        return response()->json(['success' => true]);
    }

    public function apiUnreadCount(): JsonResponse
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    public function apiRealtimeSummary(): JsonResponse
    {
        $userId = Auth::id();

        $unreadQuery = Message::where('receiver_id', $userId)
            ->whereNull('read_at');

        $unreadCount = (clone $unreadQuery)->count();

        $latestUnread = (clone $unreadQuery)
            ->with('sender')
            ->latest('created_at')
            ->first();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'latest_unread' => $latestUnread ? [
                'id' => $latestUnread->id,
                'sender_id' => $latestUnread->sender_id,
                'sender_name' => $latestUnread->sender?->name ?? 'Unknown',
                'sender_avatar' => $latestUnread->sender?->profile_photo_url,
                'body' => $latestUnread->body,
                'attachment_type' => $latestUnread->attachment_type,
                'created_at' => optional($latestUnread->created_at)->toIso8601String(),
            ] : null,
        ]);
    }

    public function apiAvailableUsers(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $search = $request->input('search', '');

            // Debug logging
            \Log::info('apiAvailableUsers called', [
                'user_id' => $userId,
                'search' => $search,
            ]);

            // Fetch all users except the currently logged-in user
            $query = User::where('id', '!=', $userId)->orderBy('name');

            // Apply search filter if provided
            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', $searchTerm)
                      ->orWhere('email', 'LIKE', $searchTerm);
                });
            }

            $allUsers = $query->get();

            \Log::info('apiAvailableUsers users found', [
                'count' => $allUsers->count(),
                'search' => $search,
            ]);

            // Format response
            $users = $allUsers->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'avatar' => $u->profile_photo_url,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'users' => $users,
                'total' => $users->count(),
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in apiAvailableUsers', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'users' => [],
                'error' => 'Unable to load users',
            ], 500);
        }
    }

    public function apiPresenceHeartbeat(Request $request): JsonResponse
    {
        $request->validate([
            'active_conversation_id' => 'nullable|integer|exists:users,id',
        ]);

        $userId = Auth::id();

        Cache::put($this->onlineCacheKey($userId), now()->timestamp, now()->addSeconds(self::ONLINE_TTL_SECONDS));

        $activeConversationId = $request->integer('active_conversation_id');
        if ($activeConversationId > 0 && $this->canViewConversation($activeConversationId)) {
            Cache::put(
                $this->activeConversationCacheKey($userId),
                $activeConversationId,
                now()->addSeconds(self::ONLINE_TTL_SECONDS)
            );
        }

        return response()->json([
            'success' => true,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function apiPresence(Request $request): JsonResponse
    {
        $ids = collect($request->input('ids', []));

        if ($ids->isEmpty()) {
            $csv = trim((string) $request->input('ids', ''));
            if ($csv !== '') {
                $ids = collect(explode(',', $csv));
            }
        }

        $requestedIds = $ids
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(100)
            ->values();

        $presence = [];
        foreach ($requestedIds as $targetId) {
            if ($targetId !== Auth::id() && ! $this->canViewConversation($targetId)) {
                continue;
            }

            $lastOnline = Cache::get($this->onlineCacheKey($targetId));
            $activeConversation = Cache::get($this->activeConversationCacheKey($targetId));

            $presence[(string) $targetId] = [
                'online' => is_numeric($lastOnline) && (now()->timestamp - (int) $lastOnline) <= self::ONLINE_TTL_SECONDS,
                'last_online' => $lastOnline ? (int) $lastOnline : null,
                'active_conversation_id' => $activeConversation ? (int) $activeConversation : null,
            ];
        }

        return response()->json([
            'success' => true,
            'presence' => $presence,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function apiTypingUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id|not_in:'.Auth::id(),
            'typing' => 'required|boolean',
        ]);

        $receiverId = (int) $request->input('receiver_id');
        if (! $this->canMessageUser($receiverId)) {
            return response()->json(['error' => 'You cannot message this user'], 403);
        }

        $cacheKey = $this->typingCacheKey($receiverId, Auth::id());
        if ($request->boolean('typing')) {
            Cache::put($cacheKey, now()->timestamp, now()->addSeconds(self::TYPING_TTL_SECONDS));
        } else {
            Cache::forget($cacheKey);
        }

        return response()->json(['success' => true]);
    }

    public function apiTypingStatuses(Request $request): JsonResponse
    {
        $ids = collect($request->input('ids', []));

        if ($ids->isEmpty()) {
            $csv = trim((string) $request->input('ids', ''));
            if ($csv !== '') {
                $ids = collect(explode(',', $csv));
            }
        }

        $requestedIds = $ids
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(100)
            ->values();

        $typing = [];
        foreach ($requestedIds as $targetId) {
            if (! $this->canViewConversation($targetId)) {
                continue;
            }

            $timestamp = Cache::get($this->typingCacheKey(Auth::id(), $targetId));
            $typing[(string) $targetId] = is_numeric($timestamp) && (now()->timestamp - (int) $timestamp) <= self::TYPING_TTL_SECONDS;
        }

        return response()->json([
            'success' => true,
            'typing' => $typing,
        ]);
    }

    public function apiTypingStatus(User $user): JsonResponse
    {
        if (! $this->canViewConversation($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $timestamp = Cache::get($this->typingCacheKey(Auth::id(), $user->id));
        $isTyping = is_numeric($timestamp) && (now()->timestamp - (int) $timestamp) <= self::TYPING_TTL_SECONDS;

        return response()->json([
            'success' => true,
            'typing' => $isTyping,
            'updated_at' => $timestamp ? (int) $timestamp : null,
        ]);
    }

    private function onlineCacheKey(int $userId): string
    {
        return 'chat:online:'.$userId;
    }

    private function activeConversationCacheKey(int $userId): string
    {
        return 'chat:active-conversation:'.$userId;
    }

    private function typingCacheKey(int $receiverId, int $senderId): string
    {
        return 'chat:typing:'.$receiverId.':'.$senderId;
    }
}

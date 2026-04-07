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
use Illuminate\View\View;

class MessageController extends Controller
{
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
            $coordinators = User::where('role', User::ROLE_COORDINATOR)->get();
            $studentIds = Assignment::where('supervisor_id', $user->id)->pluck('student_id');
            $students = User::whereIn('id', $studentIds)->get();
            return $coordinators->merge($students)->sortBy('name');
        } elseif ($user->role === User::ROLE_COORDINATOR) {
            return User::where('role', User::ROLE_SUPERVISOR)->orderBy('name')->get();
        } elseif ($user->role === User::ROLE_STUDENT) {
            $supervisorIds = Assignment::where('student_id', $user->id)->pluck('supervisor_id');
            return User::whereIn('id', $supervisorIds)->orderBy('name')->get();
        } else {
            return User::where('id', '!=', $user->id)->orderBy('name')->get();
        }
    }
}

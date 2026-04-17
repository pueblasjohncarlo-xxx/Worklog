<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $notifications = $user->notifications()
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->back();
        }

        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();

            // Redirect to the URL in the notification data
            if (isset($notification->data['url'])) {
                return redirect($notification->data['url']);
            }
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return redirect()->back();
    }

    public function apiSummary(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false], 401);
        }

        $limit = (int) $request->query('limit', 10);
        if ($limit < 1) {
            $limit = 1;
        }
        if ($limit > 25) {
            $limit = 25;
        }

        $unreadQuery = $user->unreadNotifications()->latest();
        $unreadCount = (clone $unreadQuery)->count();
        $unread = (clone $unreadQuery)->take($limit)->get();

        $items = $unread->map(function ($notification) {
            $data = (array) ($notification->data ?? []);

            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? null,
                'title' => $data['title'] ?? $data['subject'] ?? 'Notification',
                'content' => $data['content'] ?? '',
                'url' => $data['url'] ?? null,
                'read_url' => route('notifications.read', $notification->id),
                'created_at' => optional($notification->created_at)->toIso8601String(),
                'created_at_human' => optional($notification->created_at)->diffForHumans(),
            ];
        })->values();

        $latestUnread = $items->first();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'latest_unread' => $latestUnread,
            'items' => $items,
        ]);
    }
}

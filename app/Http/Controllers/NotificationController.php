<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
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
}

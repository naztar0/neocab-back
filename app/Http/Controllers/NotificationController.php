<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function show(Notification $notification): Response
    {
        $this->authorize($notification);
        return response($notification);
    }

    public function destroy(Notification $notification): Response
    {
        $this->authorize($notification);
        return response($notification->delete());
    }

    public function getUserNotifications(User $user): Response
    {
        $this->authorize('getUserNotifications', $user);
        return response($user->notifications()->orderByDesc('created_at')->limit(100)->get());
    }

    public function createUserNotification(User $user): Response
    {
        $this->authorize('createUserNotification', User::class);
        return response($user->notifications()->create(request()->all()));
    }

    public function markAsReadNotification(Notification $notification): Response
    {
        $this->authorize('markAsReadNotification', $notification);
        return response(tap($notification)->update(['read' => true]));
    }
}

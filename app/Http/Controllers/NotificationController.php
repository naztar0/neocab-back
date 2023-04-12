<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function show(Notification $notification): Response
    {
        return response($notification);
    }

    public function destroy(Notification $notification): Response
    {
        return response($notification->delete());
    }

    public function getUserNotifications(User $user): Response
    {
        return response($user->notifications()->orderByDesc('created_at')->limit(100)->get());
    }

    public function createUserNotification(User $user): Response
    {
        return response($user->notifications()->create(request()->all()));
    }

    public function markAsReadNotification(Notification $notification): Response
    {
        return response(tap($notification)->update(['read' => true]));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // 1. Gửi thông báo
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'notification_type' => 'required|string',
            'related_entity_type' => 'required|string',
            'related_entity_id' => 'required|integer',
            'title' => 'required|string',
            'message' => 'required|string',
            'priority' => 'in:low,medium,high',
        ]);

        $notification = Notification::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'notification_type' => $request->notification_type,
            'related_entity_type' => $request->related_entity_type,
            'related_entity_id' => $request->related_entity_id,
            'title' => $request->title,
            'message' => $request->message,
            'priority' => $request->priority ?? 'medium',
        ]);

        return response()->json(['message' => 'Notification sent', 'data' => $notification], 201);
    }

    // 2. Lấy danh sách thông báo của người dùng
    public function index()
    {
        $notifications = Notification::where('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // 3. Đánh dấu đã đọc
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function myNotifications()
{
    return Notification::where('receiver_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();
}
}

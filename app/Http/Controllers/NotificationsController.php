<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function getNotifications(Request $request)
    {
        try {
            $user = auth()->user();
            $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
            return response()->json($notifications, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã có lỗi xảy ra trong quá trình lấy thông báo"
            ], 500);
        }
    }

    public function getUnreadCount(Request $request)
    {
        try {
            $user = auth()->user();
            $unReadCount = $user->unreadNotifications()->count();
            return response()->json(['unread_count' => $unReadCount], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã có lỗi xảy ra trong quá trình đếm thông báo"
            ], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $notifications = $user->notifications()->where('id', $id)->firstOrFail();
            $notifications->markAsRead();
            $notifications->update(['read_at' => now()]);
            return response()->json(['message' => 'Đã đánh dấu thông báo là đã đọc'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã có lỗi xảy ra trong quá trình đọc thông báo"
            ], 500);
        }
    }

    public function readAll(Request $request)
    {
        try {
            $user = auth()->user();
            foreach ($user->unreadNotifications as $notification) {
                $notification->markAsRead();
                $notification->update(['read_at' => now()]);
            }
            return response()->json(['message' => 'Đã đánh dấu thông báo là đã đọc tất cả'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã có lỗi xảy ra trong quá trình đọc thông báo"
            ], 500);
        }
    }

    public function deleteNotifications(Request $request)
    {
        try {
            $user = auth()->user();
            $user->notifications()->delete();
            return response()->json(['message' => 'Đã xoá thông báo thành công'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã có lỗi xảy ra trong quá trình đọc thông báo"
            ], 500);
        }
    }
}

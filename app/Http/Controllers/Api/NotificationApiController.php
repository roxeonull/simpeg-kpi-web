<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * Get paginated notifications for the authenticated user.
     * GET /api/notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = AppNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 30));

        $unreadCount = AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Mark a single notification as read.
     * POST /api/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notification = AppNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi ditandai telah dibaca.',
        ]);
    }

    /**
     * Mark all notifications as read.
     * POST /api/notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai telah dibaca.',
        ]);
    }
}

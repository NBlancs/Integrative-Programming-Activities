<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->notifications,
        ], 200);
    }

    /**
     * Get unread notifications for the authenticated user.
     */
    public function unread(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->unreadNotifications,
        ], 200);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ], 200);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All unread notifications marked as read',
        ], 200);
    }
}

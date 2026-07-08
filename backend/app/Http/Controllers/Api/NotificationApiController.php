<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->limit(50)->get()->map(fn($n) => [
            'id'         => $n->id,
            'type'       => class_basename($n->type),
            'data'       => $n->data,
            'read_at'    => $n->read_at,
            'created_at' => $n->created_at,
        ]);

        return response()->json([
            'notifications'  => $notifications,
            'unread_count'   => $user->unreadNotifications()->count(),
        ]);
    }

    public function lire(Request $request)
    {
        $user = Auth::user();

        if ($request->filled('id')) {
            $user->notifications()->where('id', $request->id)->update(['read_at' => now()]);
        } else {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['message' => 'Notifications marquées comme lues.']);
    }
}

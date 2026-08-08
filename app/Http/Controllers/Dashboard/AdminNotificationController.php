<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class AdminNotificationController extends Controller
{
public function fetch()
{
    $items = AdminNotification::with('order:id,order_number')->latest()->take(10)->get();
    $unreadCount = AdminNotification::unread()->count();

    return response()->json([
        'unread_count' => $unreadCount,
        'items' => $items->map(fn($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'body' => $n->body,
            'is_read' => $n->is_read,
            'order_id' => $n->order_id,
            'order_number' => $n->order?->order_number,
            'time' => $n->created_at->diffForHumans(),
        ]),
    ]);
}

    public function markAllRead()
    {
        AdminNotification::unread()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}

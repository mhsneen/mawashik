<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function fetch()
    {
        // جلب التنبيهات غير المقروءة فقط بحيث التنبيه المفتوح يختفي من القائمة
        $items = AdminNotification::unread()->latest()->take(15)->get();
        $unreadCount = $items->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items->map(fn($n) => [
                'id'       => $n->id,
                'title'    => $n->title,
                'body'     => $n->body,
                'is_read'  => $n->is_read,
                'url'      => route('admin.notifications.open', $n->id),
                'icon'     => $n->icon ?? 'bx bx-bell',
                'color'    => $n->color ?? 'primary',
                'time'     => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function open($id)
    {
        $notification = AdminNotification::find($id);

        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            if (!empty($notification->url) && $notification->url !== '#') {
                return redirect($notification->url);
            }
        }

        return redirect()->back();
    }

    public function markAllRead()
    {
        AdminNotification::unread()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}

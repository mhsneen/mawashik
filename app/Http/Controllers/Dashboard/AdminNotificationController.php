<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function fetch()
    {
        // لا نحتاج لجلب الـ order لأن الرابط أصبح مخزن في حقل url
        $items = AdminNotification::latest()->take(15)->get();
        $unreadCount = AdminNotification::unread()->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items->map(fn($n) => [
                'id'       => $n->id,
                'title'    => $n->title,
                'body'     => $n->body,
                'is_read'  => $n->is_read,
                'url'      => $n->url ?? '#', // الرابط الجاهز
                'icon'     => $n->icon ?? 'bx bx-bell', // الأيقونة
                'color'    => $n->color ?? 'primary', // اللون
                'time'     => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function markAllRead()
    {
        AdminNotification::unread()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}

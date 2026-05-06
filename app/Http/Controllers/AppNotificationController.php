<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->string('filter')->toString();

        $notifications = AppNotification::query()
            ->where('user_id', auth()->id())
            ->when($filter === 'unread', function ($query) {
                $query->whereNull('read_at');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', compact('notifications', 'filter'));
    }

    public function markAsRead(AppNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update([
            'read_at' => now(),
        ]);

        if ($notification->link_url) {
            return redirect($notification->link_url);
        }

        return back()->with('success', 'อ่านแจ้งเตือนแล้ว');
    }

    public function markAllAsRead()
    {
        AppNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return back()->with('success', 'อ่านแจ้งเตือนทั้งหมดแล้ว');
    }

    public function destroy(AppNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->delete();

        return back()->with('success', 'ลบแจ้งเตือนเรียบร้อยแล้ว');
    }
}

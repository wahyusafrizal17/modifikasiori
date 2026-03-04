<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($notif) {
                $isWarehouseContext = auth()->user()?->section === 'warehouse' || session('admin_section') === 'warehouse';
                if ($notif->type === 'mutasi_produk' && $isWarehouseContext && $notif->link) {
                    $notif->link = preg_replace(
                        '#/warehouse/mutasi-masuk/(\d+)#',
                        '/warehouse/transaksi-speedshop/$1',
                        $notif->link
                    );
                }
                return $notif;
            });

        $unreadCount = AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(AppNotification $appNotification)
    {
        if ($appNotification->user_id !== auth()->id()) {
            abort(403);
        }

        $appNotification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}

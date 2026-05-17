<?php

namespace Modules\Notifications\Http\Controllers;

use App\Core\Activity\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Notifications\Models\EnterpriseNotification;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications::notifications.index', [
            'notifications' => EnterpriseNotification::query()
                ->where('user_id', $request->user()?->id)
                ->latest()
                ->paginate(20),
            'unreadCount' => EnterpriseNotification::query()
                ->where('user_id', $request->user()?->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function markRead(
        Request $request,
        EnterpriseNotification $notification,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        abort_unless((int) $notification->user_id === (int) $request->user()?->id, 403);

        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();

            $activityLogger->log(
                'notifications.marked_read',
                $request->user(),
                $notification,
                metadata: ['notification_type' => $notification->type],
                newValues: ['read_at' => $notification->read_at?->toISOString()],
                request: $request,
            );
        }

        return back()->with('status', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $updated = EnterpriseNotification::query()
            ->where('user_id', $request->user()?->id)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'updated_at' => now()]);

        if ($updated > 0) {
            $activityLogger->log(
                'notifications.marked_all_read',
                $request->user(),
                metadata: ['updated' => $updated],
                request: $request,
            );
        }

        return back()->with('status', 'Semua notifikasi ditandai sudah dibaca.');
    }
}

<?php

namespace Modules\Holding\Http\Controllers;

use App\Core\Approvals\ApprovalInboxService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Holding\Models\Brand;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Notifications\Models\EnterpriseNotification;

class HoldingDashboardController extends Controller
{
    public function __invoke(Request $request, ApprovalInboxService $approvalInbox): View
    {
        $user = $request->user();

        return view('holding::dashboard', [
            'activeBrands' => Brand::query()->where('is_active', true)->count(),
            'pendingApprovals' => $user ? $approvalInbox->countForUser($user) : 0,
            'criticalStock' => WarehouseStock::query()
                ->where('reorder_level', '>', 0)
                ->whereColumn('on_hand', '<=', 'reorder_level')
                ->count(),
            'unreadNotifications' => $user
                ? EnterpriseNotification::query()->where('user_id', $user->id)->whereNull('read_at')->count()
                : 0,
        ]);
    }
}

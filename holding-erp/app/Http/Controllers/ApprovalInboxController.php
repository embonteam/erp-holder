<?php

namespace App\Http\Controllers;

use App\Core\Approvals\ApprovalInboxService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalInboxController extends Controller
{
    public function __invoke(Request $request, ApprovalInboxService $approvalInbox): View
    {
        return view('approvals.index', [
            'approvalItems' => $approvalInbox->forUser($request->user()),
        ]);
    }
}

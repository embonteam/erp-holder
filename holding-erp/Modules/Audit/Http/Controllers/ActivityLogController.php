<?php

namespace Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Audit\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('event'), function ($query) use ($request): void {
                $query->where('event', 'like', '%'.$request->string('event')->toString().'%');
            })
            ->when($request->filled('subject'), function ($query) use ($request): void {
                $subject = $request->string('subject')->toString();

                $query->where(function ($query) use ($subject): void {
                    $query->where('subject_type', 'like', '%'.$subject.'%')
                        ->orWhere('subject_id', $subject);
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('audit::activity_logs.index', [
            'logs' => $logs,
            'events' => ActivityLog::query()->select('event')->distinct()->orderBy('event')->pluck('event'),
            'filters' => $request->only(['event', 'subject']),
        ]);
    }

    public function show(ActivityLog $activityLog): View
    {
        return view('audit::activity_logs.show', [
            'activityLog' => $activityLog->load('user'),
        ]);
    }
}

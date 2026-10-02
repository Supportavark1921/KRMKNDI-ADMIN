<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('audit-log.view');

        $query = Activity::with('causer', 'subject')
            ->latest();

        if ($event = $request->query('event')) {
            $query->where('event', $event);
        }
        if ($causer = $request->query('causer_id')) {
            $query->where('causer_id', $causer)->where('causer_type', 'App\\Models\\User');
        }
        if ($subject = $request->query('subject_type')) {
            $query->where('subject_type', 'like', "%{$subject}%");
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return view('admin.audit.index', [
            'activities' => $query->paginate(40)->withQueryString(),
            'events' => Activity::distinct()->pluck('event')->filter()->sort()->values(),
        ]);
    }

    public function show(Activity $activity): View
    {
        Gate::authorize('audit-log.view');
        $activity->load('causer', 'subject');

        return view('admin.audit.show', compact('activity'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ValidatesPerPage;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use ValidatesPerPage;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view_activity_logs'), 403);

        $sortField = in_array($request->sort, ['created_at', 'action', 'module'])
                     ? $request->sort : 'created_at';
        $sortDir   = $request->direction === 'asc' ? 'asc' : 'desc';

        $logs = ActivityLog::with('user')
            ->when($request->search, fn($q) => $q->where('description', 'like', "%{$request->search}%")
                ->orWhere('action', 'like', "%{$request->search}%"))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->module, fn($q) => $q->where('module', $request->module))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy($sortField, $sortDir)
            ->paginate($this->getPerPage($request, 20))
            ->withQueryString();

        $users   = User::orderBy('name')->get(['id', 'name']);
        $modules = ActivityLog::distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::distinct()->orderBy('action')->pluck('action');

        return view('activity-logs.index', compact('logs', 'users', 'modules', 'actions', 'sortField', 'sortDir'));
    }
}

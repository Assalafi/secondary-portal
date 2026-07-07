<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        // Filter by IP address
        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get filter options from database
        $modules = AuditLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $events = AuditLog::select('event')->distinct()->orderBy('event')->pluck('event');
        $roles = AuditLog::select('user_role')->whereNotNull('user_role')->distinct()->orderBy('user_role')->pluck('user_role');
        $users = User::select('id', 'name')->whereIn('id', AuditLog::select('user_id')->whereNotNull('user_id')->distinct())->orderBy('name')->get();
        $ipAddresses = AuditLog::select('ip_address')->whereNotNull('ip_address')->distinct()->orderBy('ip_address')->pluck('ip_address');

        // Stats
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'logins_today' => AuditLog::where('event', 'login')->whereDate('created_at', today())->count(),
            'changes_today' => AuditLog::whereIn('event', ['created', 'updated', 'deleted'])->whereDate('created_at', today())->count(),
        ];

        // Active filters count
        $activeFilters = collect(['module', 'event', 'user_id', 'role', 'ip_address', 'date_from', 'date_to', 'search'])
            ->filter(fn($key) => $request->filled($key))->count();

        return view('admin.audit-logs.index', compact('logs', 'modules', 'events', 'roles', 'users', 'ipAddresses', 'stats', 'activeFilters'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        return view('admin.audit-logs.show', compact('log'));
    }
}

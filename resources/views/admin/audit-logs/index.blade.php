@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Audit Logs</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Audit Logs</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;min-width:48px;">
                        <span class="material-symbols-outlined">analytics</span>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Logs</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;min-width:48px;">
                        <span class="material-symbols-outlined">today</span>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Today's Activity</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['today']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;min-width:48px;">
                        <span class="material-symbols-outlined">login</span>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Logins Today</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['logins_today']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;min-width:48px;">
                        <span class="material-symbols-outlined">edit_note</span>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Changes Today</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['changes_today']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" id="filterForm">
                <!-- Search + Quick Filters Row -->
                <div class="row g-3 mb-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-medium">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><span class="material-symbols-outlined" style="font-size:18px;">search</span></span>
                            <input type="text" class="form-control border-start-0" name="search" value="{{ request('search') }}" placeholder="Search description, user, IP, URL...">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">User</label>
                        <select class="form-select" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">Role</label>
                        <select class="form-select" name="role">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">Event</label>
                        <select class="form-select" name="event">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ ucfirst($event) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">Module</label>
                        <select class="form-select" name="module">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $module)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Advanced Filters Row -->
                <div class="row g-3 mb-3 align-items-end">
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">Date From</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">Date To</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium">IP Address</label>
                        <select class="form-select" name="ip_address">
                            <option value="">All IPs</option>
                            @foreach($ipAddresses as $ip)
                                <option value="{{ $ip }}" {{ request('ip_address') == $ip ? 'selected' : '' }}>{{ $ip }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">filter_alt</span>Apply Filters
                            </button>
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary">
                                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">refresh</span>Reset
                            </a>
                        </div>
                    </div>
                </div>

                @if($activeFilters > 0)
                <div class="border-top pt-2 pb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="small text-muted fw-medium">Active Filters ({{ $activeFilters }}):</span>
                        @if(request('search'))
                            <span class="badge bg-primary bg-opacity-10 text-primary d-flex align-items-center gap-1">Search: "{{ request('search') }}" <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['search' => ''])) }}" class="text-primary">&times;</a></span>
                        @endif
                        @if(request('user_id'))
                            <span class="badge bg-info bg-opacity-10 text-info d-flex align-items-center gap-1">User: {{ $users->firstWhere('id', request('user_id'))->name ?? request('user_id') }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['user_id' => ''])) }}" class="text-info">&times;</a></span>
                        @endif
                        @if(request('role'))
                            <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center gap-1">Role: {{ request('role') }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['role' => ''])) }}" class="text-warning">&times;</a></span>
                        @endif
                        @if(request('event'))
                            <span class="badge bg-success bg-opacity-10 text-success d-flex align-items-center gap-1">Event: {{ ucfirst(request('event')) }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['event' => ''])) }}" class="text-success">&times;</a></span>
                        @endif
                        @if(request('module'))
                            <span class="badge bg-secondary bg-opacity-10 text-secondary d-flex align-items-center gap-1">Module: {{ ucfirst(str_replace('-', ' ', request('module'))) }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['module' => ''])) }}" class="text-secondary">&times;</a></span>
                        @endif
                        @if(request('ip_address'))
                            <span class="badge bg-dark bg-opacity-10 text-dark d-flex align-items-center gap-1">IP: {{ request('ip_address') }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['ip_address' => ''])) }}" class="text-dark">&times;</a></span>
                        @endif
                        @if(request('date_from'))
                            <span class="badge bg-primary bg-opacity-10 text-primary d-flex align-items-center gap-1">From: {{ request('date_from') }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['date_from' => ''])) }}" class="text-primary">&times;</a></span>
                        @endif
                        @if(request('date_to'))
                            <span class="badge bg-primary bg-opacity-10 text-primary d-flex align-items-center gap-1">To: {{ request('date_to') }} <a href="{{ route('admin.audit-logs.index', array_diff_key(request()->query(), ['date_to' => ''])) }}" class="text-primary">&times;</a></span>
                        @endif
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Logs Timeline -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($logs->isEmpty())
                <div class="text-center py-5">
                    <span class="material-symbols-outlined" style="font-size:64px;color:#ccc;">history</span>
                    <h6 class="mt-3 text-muted">No audit logs found</h6>
                    <p class="text-muted small">Activity logs will appear here as users interact with the system.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4" style="width:180px;">Timestamp</th>
                                <th style="width:160px;">User</th>
                                <th style="width:100px;">Event</th>
                                <th style="width:120px;">Module</th>
                                <th>Description</th>
                                <th style="width:120px;">IP Address</th>
                                <th style="width:60px;" class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td class="ps-4">
                                    <div class="small fw-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $log->created_at->format('h:i:s A') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:32px;height:32px;min-width:32px;font-size:12px;background:{{ $log->user_role == 'Admin' ? '#4f46e5' : ($log->user_role == 'Teacher' ? '#0891b2' : ($log->user_role == 'Student' ? '#059669' : ($log->user_role == 'Parent' ? '#d97706' : '#6b7280'))) }}">
                                            {{ strtoupper(substr($log->user_name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="small fw-medium text-truncate" style="max-width:120px;">{{ $log->user_name ?? 'System' }}</div>
                                            <div style="font-size:10px;" class="text-muted">{{ $log->user_role ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $eventColors = [
                                            'login' => 'success',
                                            'logout' => 'secondary',
                                            'created' => 'primary',
                                            'updated' => 'info',
                                            'deleted' => 'danger',
                                            'approved' => 'success',
                                            'published' => 'success',
                                            'registered' => 'primary',
                                            'failed_login' => 'danger',
                                        ];
                                        $badgeColor = $eventColors[$log->event] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }}" style="font-size:11px;">{{ ucfirst($log->event) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size:11px;">{{ ucfirst(str_replace('-', ' ', $log->module)) }}</span>
                                </td>
                                <td>
                                    <span class="small">{{ Str::limit($log->description, 60) }}</span>
                                </td>
                                <td>
                                    <code class="small">{{ $log->ip_address ?? '-' }}</code>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-light border" title="View Details">
                                        <span class="material-symbols-outlined" style="font-size:16px;">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="text-muted small">
                        Showing {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                    </div>
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

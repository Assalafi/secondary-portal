@extends('layouts.admin')

@section('title', 'Audit Log Detail')
@section('page-title', 'Audit Log Detail')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Audit Log Detail</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.audit-logs.index') }}" class="text-muted">Audit Logs</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Log #{{ $log->id }}</li>
            </ol>
        </nav>
    </div>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
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
                        <div class="rounded-circle bg-{{ $badgeColor }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width:56px;height:56px;min-width:56px;">
                            <span class="material-symbols-outlined text-{{ $badgeColor }}" style="font-size:28px;">
                                @switch($log->event)
                                    @case('login') login @break
                                    @case('logout') logout @break
                                    @case('created') add_circle @break
                                    @case('updated') edit @break
                                    @case('deleted') delete @break
                                    @case('approved') check_circle @break
                                    @case('published') publish @break
                                    @case('failed_login') error @break
                                    @default history
                                @endswitch
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $log->description }}</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($log->event) }}</span>
                                <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('-', ' ', $log->module)) }}</span>
                                <span class="text-muted small">{{ $log->created_at->format('M d, Y \a\t h:i:s A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td class="bg-light fw-medium" style="width:160px;">Log ID</td>
                                    <td>#{{ $log->id }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">User</td>
                                    <td>{{ $log->user_name ?? 'System' }} <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $log->user_role ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">Event</td>
                                    <td><span class="badge bg-{{ $badgeColor }}">{{ ucfirst($log->event) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">Module</td>
                                    <td>{{ ucfirst(str_replace('-', ' ', $log->module)) }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">Record Type</td>
                                    <td><code>{{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">Record ID</td>
                                    <td>{{ $log->auditable_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">URL</td>
                                    <td><code class="small">{{ $log->url ?? '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">HTTP Method</td>
                                    <td><span class="badge bg-dark">{{ $log->method ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">IP Address</td>
                                    <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-medium">Timestamp</td>
                                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }} <span class="text-muted small">({{ $log->created_at->diffForHumans() }})</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Data Changes -->
            @if($log->old_values || $log->new_values)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size:20px;">compare_arrows</span>
                        Data Changes
                    </h6>

                    @if($log->event === 'updated' && $log->old_values && $log->new_values)
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width:200px;">Field</th>
                                        <th class="text-danger">Old Value</th>
                                        <th class="text-success">New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->new_values as $field => $newValue)
                                        <tr>
                                            <td class="fw-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                            <td class="bg-danger bg-opacity-10"><code>{{ is_array($log->old_values[$field] ?? null) ? json_encode($log->old_values[$field]) : ($log->old_values[$field] ?? '-') }}</code></td>
                                            <td class="bg-success bg-opacity-10"><code>{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</code></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($log->event === 'created' && $log->new_values)
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width:200px;">Field</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->new_values as $field => $value)
                                        @if(!in_array($field, ['id', 'created_at', 'updated_at']))
                                        <tr>
                                            <td class="fw-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                            <td><code>{{ is_array($value) ? json_encode($value) : $value }}</code></td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($log->event === 'deleted' && $log->old_values)
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width:200px;">Field</th>
                                        <th>Deleted Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->old_values as $field => $value)
                                        @if(!in_array($field, ['id', 'created_at', 'updated_at']))
                                        <tr>
                                            <td class="fw-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                            <td class="bg-danger bg-opacity-10"><code>{{ is_array($value) ? json_encode($value) : $value }}</code></td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @if($log->new_values)
                            <h6 class="small fw-bold text-muted mb-2">Data</h6>
                            <pre class="bg-light p-3 rounded small mb-0" style="max-height:300px;overflow:auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Side Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size:20px;">person</span>
                        User Information
                    </h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold" style="width:48px;height:48px;min-width:48px;">
                            {{ strtoupper(substr($log->user_name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $log->user_name ?? 'System' }}</h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $log->user_role ?? 'System' }}</span>
                        </div>
                    </div>
                    @if($log->user)
                        <div class="small text-muted">
                            <p class="mb-1"><strong>Email:</strong> {{ $log->user->email }}</p>
                            <p class="mb-0"><strong>User ID:</strong> #{{ $log->user->id }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size:20px;">devices</span>
                        Request Information
                    </h6>
                    <div class="small">
                        <p class="mb-2"><strong>IP Address:</strong> <code>{{ $log->ip_address ?? '-' }}</code></p>
                        <p class="mb-2"><strong>Method:</strong> <span class="badge bg-dark">{{ $log->method ?? '-' }}</span></p>
                        <p class="mb-0"><strong>User Agent:</strong></p>
                        <p class="text-muted small mb-0" style="word-break:break-all;">{{ Str::limit($log->user_agent ?? '-', 200) }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary w-100">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">arrow_back</span>
                Back to Audit Logs
            </a>
        </div>
    </div>
</div>
@endsection

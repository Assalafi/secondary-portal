@extends('layouts.admin')

@section('title', 'Backup & Restore')

@section('content')
    <div class="container-fluid">
        <!-- Page Title & Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">Backup & Restore</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Backup & Restore</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Backup & Restore</h5>
                    </div>
                    <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="text-center p-4 border rounded">
                                        <i class="ri-download-cloud-line display-4 text-success mb-3"></i>
                                        <h6>Create Backup</h6>
                                        <p class="text-muted">Export database backup</p>
                                        <button class="btn btn-success">Create Backup</button>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-center p-4 border rounded">
                                        <i class="ri-upload-cloud-line display-4 text-warning mb-3"></i>
                                        <h6>Restore Database</h6>
                                        <p class="text-muted">Upload backup file</p>
                                        <input type="file" class="form-control mb-2" accept=".sql">
                                        <button class="btn btn-warning">Restore</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

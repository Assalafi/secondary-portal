@extends('layouts.admin')

@section('title', 'System Configuration')

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
                            <li class="breadcrumb-item active">System Configuration</li>
                        </ol>
                    </div>
                    <h4 class="page-title">System Configuration</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Configuration</h5>
                    </div>
                    <div class="card-body">
                            <form id="systemConfigForm">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Application Name</label>
                                            <input type="text" class="form-control" name="app_name" value="Secondary School Portal">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Time Zone</label>
                                            <select class="form-select" name="timezone">
                                                <option value="UTC">UTC</option>
                                                <option value="Africa/Lagos" selected>West Africa Time</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Date Format</label>
                                            <select class="form-select" name="date_format">
                                                <option value="d/m/Y" selected>DD/MM/YYYY</option>
                                                <option value="Y-m-d">YYYY-MM-DD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Currency</label>
                                            <select class="form-select" name="currency">
                                                <option value="NGN" selected>Nigerian Naira (₦)</option>
                                                <option value="USD">US Dollar ($)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>Save Configuration
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

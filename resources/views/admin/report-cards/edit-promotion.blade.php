@extends('layouts.admin')

@section('title', 'Edit Promotion - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Promotion Decision - {{ $reportCard->student->full_name }}</h5>
                    <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-sm btn-secondary float-end">Back to Report Card</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.academic-management.report-cards.update-promotion', $reportCard->id) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Student Information</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <td><strong>Student:</strong> {{ $reportCard->student->full_name }}</td>
                                        <td><strong>Current Class:</strong> {{ $reportCard->class->name }}</td>
                                        <td><strong>Current Average:</strong> {{ number_format($reportCard->average_score, 2) }}%</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <strong>Auto-Calculate Promotion:</strong> 
                            <form action="{{ route('admin.academic-management.report-cards.auto-calculate-promotion', $reportCard->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Auto-Calculate Based on Performance</button>
                            </form>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="promotion_decision" class="form-label">Promotion Decision</label>
                                <select class="form-select" id="promotion_decision" name="promotion_decision" required>
                                    <option value="">Select Decision</option>
                                    <option value="Promoted" {{ $reportCard->promotion_decision == 'Promoted' ? 'selected' : '' }}>Promoted</option>
                                    <option value="Promoted on Trial" {{ $reportCard->promotion_decision == 'Promoted on Trial' ? 'selected' : '' }}>Promoted on Trial</option>
                                    <option value="Repeated" {{ $reportCard->promotion_decision == 'Repeated' ? 'selected' : '' }}>Repeated</option>
                                    <option value="Withdrawn" {{ $reportCard->promotion_decision == 'Withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                    <option value="Transferred" {{ $reportCard->promotion_decision == 'Transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="Graduated" {{ $reportCard->promotion_decision == 'Graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="Not Applicable" {{ $reportCard->promotion_decision == 'Not Applicable' ? 'selected' : '' }}>Not Applicable</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="next_class_id" class="form-label">Next Class</label>
                                <select class="form-select" id="next_class_id" name="next_class_id">
                                    <option value="">Select Next Class</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $reportCard->next_class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vacation_date" class="form-label">Vacation Date</label>
                                <input type="date" class="form-control" id="vacation_date" name="vacation_date" value="{{ $reportCard->vacation_date ? $reportCard->vacation_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="next_term_begins" class="form-label">Next Term Begins</label>
                                <input type="date" class="form-control" id="next_term_begins" name="next_term_begins" value="{{ $reportCard->next_term_begins ? $reportCard->next_term_begins->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="next_term_fee" class="form-label">Next Term Fee (₦)</label>
                                <input type="number" class="form-control" id="next_term_fee" name="next_term_fee" step="0.01" value="{{ $reportCard->next_term_fee }}">
                            </div>
                            <div class="col-md-6">
                                <label for="outstanding_balance" class="form-label">Outstanding Balance (₦)</label>
                                <input type="number" class="form-control" id="outstanding_balance" name="outstanding_balance" step="0.01" value="{{ $reportCard->outstanding_balance }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Promotion Decision</button>
                            <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

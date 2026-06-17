@extends('layouts.admin')

@section('title', 'Edit Comments - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Comments - {{ $reportCard->student->full_name }}</h5>
                    <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-sm btn-secondary float-end">Back to Report Card</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.academic-management.report-cards.update-comments', $reportCard->id) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Student Information</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <td><strong>Student:</strong> {{ $reportCard->student->full_name }}</td>
                                        <td><strong>Class:</strong> {{ $reportCard->class->name }}</td>
                                        <td><strong>Term:</strong> {{ $reportCard->term->name ?? 'Annual' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="class_teacher_comment" class="form-label">Class Teacher's Comment</label>
                            <textarea class="form-control" id="class_teacher_comment" name="class_teacher_comment" rows="4">{{ $reportCard->class_teacher_comment }}</textarea>
                            <small class="form-text text-muted">Provide constructive feedback on the student's academic performance and behavior.</small>
                        </div>

                        <div class="mb-3">
                            <label for="principal_comment" class="form-label">Principal's Comment</label>
                            <textarea class="form-control" id="principal_comment" name="principal_comment" rows="4">{{ $reportCard->principal_comment }}</textarea>
                            <small class="form-text text-muted">Principal's approval comment and recommendations.</small>
                        </div>

                        <div class="mb-3">
                            <label for="parent_comment" class="form-label">Parent's Comment</label>
                            <textarea class="form-control" id="parent_comment" name="parent_comment" rows="4" placeholder="To be filled by parent">{{ $reportCard->parent_comment }}</textarea>
                            <small class="form-text text-muted">Parent's acknowledgment and comments (optional).</small>
                        </div>

                        <div class="form-group mb-3">
                            <label>Quick Comment Templates</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary comment-template" data-target="class_teacher_comment" data-text="Excellent performance. Keep up the good work.">Excellent</button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary comment-template" data-target="class_teacher_comment" data-text="Good performance. There is room for improvement.">Good</button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary comment-template" data-target="class_teacher_comment" data-text="Needs improvement in certain areas. Please work harder.">Needs Improvement</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Comments</button>
                            <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.comment-template').forEach(button => {
    button.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const text = this.getAttribute('data-text');
        document.getElementById(target).value = text;
    });
});
</script>
@endsection

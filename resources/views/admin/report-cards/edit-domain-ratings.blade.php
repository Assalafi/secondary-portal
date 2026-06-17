@extends('layouts.admin')

@section('title', 'Edit Domain Ratings - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Domain Ratings - {{ $reportCard->student->full_name }}</h5>
                    <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-sm btn-secondary float-end">Back to Report Card</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.academic-management.report-cards.update-domain-ratings', $reportCard->id) }}" method="POST">
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

                        <!-- Rating Scale Guide -->
                        <div class="alert alert-info mb-4">
                            <strong>Rating Scale:</strong> 5 = Excellent, 4 = Very Good, 3 = Good, 2 = Fair, 1 = Poor
                        </div>

                        <!-- Affective Domain Ratings -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Affective Domain Ratings</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Attribute</th>
                                            <th>Rating (1-5)</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($affectiveTraits as $index => $trait)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $trait->name }}</td>
                                            <td>
                                                <select name="affective_ratings[{{ $trait->id }}]" class="form-select" required>
                                                    <option value="">Select Rating</option>
                                                    <option value="5" {{ $reportCard->affectiveRatings->where('trait_id', $trait->id)->first()->rating_value == 5 ? 'selected' : '' }}>5 - Excellent</option>
                                                    <option value="4" {{ $reportCard->affectiveRatings->where('trait_id', $trait->id)->first()->rating_value == 4 ? 'selected' : '' }}>4 - Very Good</option>
                                                    <option value="3" {{ $reportCard->affectiveRatings->where('trait_id', $trait->id)->first()->rating_value == 3 ? 'selected' : '' }}>3 - Good</option>
                                                    <option value="2" {{ $reportCard->affectiveRatings->where('trait_id', $trait->id)->first()->rating_value == 2 ? 'selected' : '' }}>2 - Fair</option>
                                                    <option value="1" {{ $reportCard->affectiveRatings->where('trait_id', $trait->id)->first()->rating_value == 1 ? 'selected' : '' }}>1 - Poor</option>
                                                </select>
                                            </td>
                                            <td>{{ $trait->description }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Psychomotor Skills Ratings -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Psychomotor Skills Ratings</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Skill</th>
                                            <th>Rating (1-5)</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($psychomotorTraits as $index => $trait)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $trait->name }}</td>
                                            <td>
                                                <select name="psychomotor_ratings[{{ $trait->id }}]" class="form-select" required>
                                                    <option value="">Select Rating</option>
                                                    <option value="5" {{ $reportCard->psychomotorRatings->where('trait_id', $trait->id)->first()->rating_value == 5 ? 'selected' : '' }}>5 - Excellent</option>
                                                    <option value="4" {{ $reportCard->psychomotorRatings->where('trait_id', $trait->id)->first()->rating_value == 4 ? 'selected' : '' }}>4 - Very Good</option>
                                                    <option value="3" {{ $reportCard->psychomotorRatings->where('trait_id', $trait->id)->first()->rating_value == 3 ? 'selected' : '' }}>3 - Good</option>
                                                    <option value="2" {{ $reportCard->psychomotorRatings->where('trait_id', $trait->id)->first()->rating_value == 2 ? 'selected' : '' }}>2 - Fair</option>
                                                    <option value="1" {{ $reportCard->psychomotorRatings->where('trait_id', $trait->id)->first()->rating_value == 1 ? 'selected' : '' }}>1 - Poor</option>
                                                </select>
                                            </td>
                                            <td>{{ $trait->description }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Ratings</button>
                            <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

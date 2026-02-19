@extends('layouts.bootstrap')

@section('title', $assignment->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.assignments', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Assignments
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Assignment Details Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-start">
        <div>
            <h3 class="mb-1">{{ $assignment->title }}</h3>
            <p class="text-muted mb-0">{{ $course->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('teacher.assignments.edit', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" 
               class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form method="POST" action="{{ route('teacher.assignments.delete', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.')">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <h6 class="text-muted mb-1">Status</h6>
                @if($assignment->is_published)
                    <span class="badge bg-success">Published</span>
                @else
                    <span class="badge bg-secondary">Draft</span>
                @endif
                @if($assignment->due_date)
                    <span class="badge bg-{{ $assignment->status_color }}">{{ $assignment->status_label }}</span>
                @endif
            </div>
            <div class="col-md-3">
                <h6 class="text-muted mb-1">Due Date</h6>
                @if($assignment->due_date)
                    <p class="mb-0">{{ $assignment->due_date->format('M d, Y h:i A') }}</p>
                @else
                    <p class="text-muted mb-0">No due date</p>
                @endif
            </div>
            <div class="col-md-3">
                <h6 class="text-muted mb-1">Maximum Points</h6>
                <p class="mb-0">{{ $assignment->max_points }} points</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted mb-1">Attachment</h6>
                @if($assignment->attachment_url)
                    <a href="{{ $assignment->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Download
                    </a>
                @else
                    <p class="text-muted mb-0">No attachment</p>
                @endif
            </div>
        </div>

        @if($assignment->description)
        <div class="mb-3">
            <h6 class="text-muted mb-2">Instructions</h6>
            <div class="p-3 bg-light rounded">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $assignment->description }}</p>
            </div>
        </div>
        @endif

        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $course->students->count() }}</h3>
                        <small>Total Students</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $submissions->count() }}</h3>
                        <small>Submitted</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $assignment->pending_submission_count }}</h3>
                        <small>Pending Review</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Student Submissions</h5>
    </div>
    <div class="card-body">
        @if($submissions->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No submissions yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $index => $submission)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($submission->student->name, 0, 1) }}
                                    </div>
                                    {{ $submission->student->name }}
                                </div>
                            </td>
                            <td>
                                @if($submission->submitted_at)
                                    {{ $submission->submitted_at->format('M d, Y h:i A') }}
                                    @if($submission->isLate())
                                        <span class="badge bg-warning text-dark">Late</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $submission->status_color }}">
                                    {{ $submission->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($submission->grade !== null)
                                    <strong>{{ $submission->grade }}/{{ $assignment->max_points }}</strong>
                                @else
                                    <span class="text-muted">Not graded</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->submitted_at)
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewSubmission{{ $submission->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Not Submitted Students -->
@if($notSubmittedStudents->count() > 0)
<div class="card">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Not Submitted ({{ $notSubmittedStudents->count() }})</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($notSubmittedStudents as $student)
                <div class="col-md-4 mb-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        {{ $student->name }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Submission View Modals -->
@foreach($submissions as $submission)
<div class="modal fade" id="viewSubmission{{ $submission->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submission - {{ $submission->student->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Submitted At:</strong> {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}
                    @if($submission->isLate())
                        <span class="badge bg-warning text-dark">Late Submission</span>
                    @endif
                </div>
                
                @if($submission->submission_text)
                    <div class="mb-3">
                        <strong>Submission Text:</strong>
                        <div class="p-3 bg-light rounded mt-2">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $submission->submission_text }}</p>
                        </div>
                    </div>
                @endif
                
                @if($submission->attachment_url)
                    <div class="mb-3">
                        <strong>Attachment:</strong>
                        <a href="{{ $submission->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                @endif
                
                @if($submission->grade !== null)
                    <div class="mb-3">
                        <strong>Grade:</strong> {{ $submission->grade }}/{{ $assignment->max_points }}
                    </div>
                @endif
                
                @if($submission->feedback)
                    <div class="mb-3">
                        <strong>Feedback:</strong>
                        <div class="p-3 bg-light rounded mt-2">
                            <p class="mb-0">{{ $submission->feedback }}</p>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endsection

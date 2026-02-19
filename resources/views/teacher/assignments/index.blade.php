@extends('layouts.bootstrap')

@section('title', 'Assignments - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>
</div>

<!-- Course Info -->
<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title">{{ $course->title }}</h3>
        <p class="card-text text-muted">Assignment Management</p>
    </div>
</div>

<!-- Create New Assignment Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Assignments</h4>
    <a href="{{ route('teacher.assignments.create', $course->id) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Assignment
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Assignments List -->
@if($assignments->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> No assignments yet. Click "Create Assignment" to get started.
</div>
@else
<div class="row">
    @foreach($assignments as $assignment)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $assignment->title }}</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($assignment->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                        
                        @if($assignment->due_date)
                            <span class="badge bg-{{ $assignment->status_color }}">
                                {{ $assignment->status_label }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('teacher.assignments.show', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('teacher.assignments.edit', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('teacher.assignments.delete', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger" 
                                        onclick="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                @if($assignment->description)
                    <p class="card-text text-muted small">
                        {{ Str::limit($assignment->description, 150) }}
                    </p>
                @endif

                <div class="row g-2 mt-2">
                    @if($assignment->due_date)
                    <div class="col-12">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-calendar-event me-2"></i>
                            <strong>Due:</strong>&nbsp;{{ $assignment->due_date->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-6">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-star me-2"></i>
                            <strong>Points:</strong>&nbsp;{{ $assignment->max_points }}
                        </div>
                    </div>
                    
                    @if($assignment->attachment_url)
                    <div class="col-6">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-paperclip me-2"></i>
                            <a href="{{ $assignment->attachment_url }}" target="_blank">Attachment</a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Submission Stats -->
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="mb-0 text-primary">{{ $assignment->submission_count }}</h4>
                            <small class="text-muted">Submissions</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h4 class="mb-0 text-warning">{{ $assignment->pending_submission_count }}</h4>
                            <small class="text-muted">Pending Review</small>
                        </div>
                    </div>
                </div>

                <a href="{{ route('teacher.assignments.show', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" 
                   class="btn btn-sm btn-outline-primary w-100 mt-3">
                    View Submissions
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

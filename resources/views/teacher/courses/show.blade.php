@extends('layouts.bootstrap')

@section('title', $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to My Courses
    </a>
</div>

<!-- Course Header -->
<div class="card mb-4">
    <div class="row g-0">
        @if($course->image_url)
        <div class="col-md-4">
            <img src="{{ $course->image_url }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $course->title }}">
        </div>
        @endif
        <div class="{{ $course->image_url ? 'col-md-8' : 'col-12' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="card-title mb-1">{{ $course->title }}</h3>
                        <div class="d-flex gap-2 flex-wrap">
                            @php
                            $typeColors = [
                            'online' => 'bg-info',
                            ];
                            $badgeClass = $typeColors[$course->class_type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $course->class_type_label }}</span>

                            @if(!$course->is_active)
                            <span class="badge bg-secondary">Inactive</span>
                            @elseif($course->hasEnded())
                            <span class="badge bg-danger">Course Ended</span>
                            @elseif($course->hasStarted())
                            <span class="badge bg-success">In Progress</span>
                            @else
                            <span class="badge bg-primary">Starting Soon</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <h4 class="text-success mb-0">${{ number_format($course->price, 2) }}</h4>
                        <small class="text-muted">{{ $course->duration }}</small>
                    </div>
                </div>

                <p class="card-text">{{ $course->description }}</p>

                <!-- Course Progress -->
                @if($course->start_date && $course->end_date)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold">Course Progress</span>
                        <span class="fw-bold">{{ $course->progress_percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        @php
                        $progress = $course->progress_percentage;
                        $progressClass = 'bg-success';
                        if ($progress < 25) $progressClass='bg-info' ;
                            elseif ($progress < 50) $progressClass='bg-primary' ;
                            elseif ($progress < 75) $progressClass='bg-warning' ;
                            @endphp
                            <div class="progress-bar {{ $progressClass }}" role="progressbar"
                            style="width: {{ $progress }}%">
                    </div>
                </div>
            </div>
            @endif

            <div class="row text-center border-top pt-3 mt-3">
                <div class="col-4">
                    <h5 class="mb-0 text-success">{{ $course->students->count() }}</h5>
                    <small class="text-muted">Students</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0 text-primary">{{ $course->start_date ? $course->start_date->format('M d, Y') : 'TBD' }}</h5>
                    <small class="text-muted">Start Date</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0 text-danger">{{ $course->end_date ? $course->end_date->format('M d, Y') : 'TBD' }}</h5>
                    <small class="text-muted">End Date</small>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Schedule Information -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card h-100 border-info">
            <div class="card-header bg-info text-white">
                <i class="bi bi-camera-video"></i> Class Schedule
            </div>
            <div class="card-body">
                @if($course->online_schedule ?? $course->schedule)
                <p class="mb-0">{{ $course->online_schedule ?? $course->schedule }}</p>
                @else
                <p class="text-muted mb-0">No schedule set</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <a href="{{ route('teacher.courses.students', $course->id) }}" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                <h5 class="mt-2 mb-0 text-dark">View Students</h5>
                <p class="text-muted small mb-0">{{ $course->students->count() }} enrolled students</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('teacher.courses.materials', $course->id) }}" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="bi bi-folder text-success" style="font-size: 2.5rem;"></i>
                <h5 class="mt-2 mb-0 text-dark">Course Materials</h5>
                <p class="text-muted small mb-0">Upload and manage files</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('teacher.courses.attendance', $course->id) }}" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-check text-warning" style="font-size: 2.5rem;"></i>
                <h5 class="mt-2 mb-0 text-dark">Attendance</h5>
                <p class="text-muted small mb-0">Mark and view attendance</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('teacher.courses.assignments', $course->id) }}" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-text text-info" style="font-size: 2.5rem;"></i>
                <h5 class="mt-2 mb-0 text-dark">Assignments</h5>
                <p class="text-muted small mb-0">Create and manage assignments</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('teacher.courses.performance', $course->id) }}" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up-arrow text-primary" style="font-size: 2.5rem;"></i>
                <h5 class="mt-2 mb-0 text-dark">Performance</h5>
                <p class="text-muted small mb-0">View student performance</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Enrolled Students -->
@if($course->students->count() > 0)
<div class="card">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> Recently Enrolled Students</span>
        <a href="{{ route('teacher.courses.students', $course->id) }}" class="btn btn-sm btn-light">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($course->students->take(5) as $student)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $student->name }}</h6>
                        <small class="text-muted">{{ $student->email }}</small>
                    </div>
                </div>
                <small class="text-muted">
                    Enrolled {{ $student->pivot->created_at ? $student->pivot->created_at->diffForHumans() : 'N/A' }}
                </small>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>
@endsection
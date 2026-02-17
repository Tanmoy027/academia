@extends('layouts.bootstrap')

@section('title', 'Students - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>
</div>

<!-- Course Header -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-book"></i> {{ $course->title }}</span>
            @php
            $typeColors = [
            'online' => 'bg-info',
            'offline' => 'bg-warning text-dark',
            'hybrid' => 'bg-purple text-white',
            ];
            $badgeClass = $typeColors[$course->class_type] ?? 'bg-secondary';
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $course->class_type_label }}</span>
        </div>
    </div>
</div>

<!-- Students List -->
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> Enrolled Students</span>
        <span class="badge bg-light text-primary">{{ $students->count() }} {{ Str::plural('Student', $students->count()) }}</span>
    </div>

    @if($students->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Enrolled Date</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <span class="fw-semibold">{{ $student->name }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:{{ $student->email }}" class="text-decoration-none">
                            {{ $student->email }}
                        </a>
                    </td>
                    <td>
                        @if($student->pivot->created_at)
                        <span title="{{ $student->pivot->created_at->format('Y-m-d H:i:s') }}">
                            {{ $student->pivot->created_at->format('M d, Y') }}
                        </span>
                        <br>
                        <small class="text-muted">{{ $student->pivot->created_at->diffForHumans() }}</small>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($course->hasEnded())
                        <span class="badge bg-secondary">Course Completed</span>
                        @elseif($course->hasStarted())
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-primary">Enrolled</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="mailto:{{ $student->email }}" class="btn btn-outline-primary" title="Send Email">
                                <i class="bi bi-envelope"></i>
                            </a>
                            <button class="btn btn-outline-secondary" title="View Profile" disabled>
                                <i class="bi bi-person"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="card-body text-center py-5">
        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
        <h5 class="mt-3 text-muted">No Students Enrolled</h5>
        <p class="text-muted">No students have enrolled in this course yet.</p>
    </div>
    @endif
</div>

<!-- Quick Stats -->
@if($students->count() > 0)
<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h3 class="text-primary mb-0">{{ $students->count() }}</h3>
                <small class="text-muted">Total Students</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h3 class="text-success mb-0">${{ number_format($course->price * $students->count(), 2) }}</h3>
                <small class="text-muted">Total Revenue</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h3 class="text-info mb-0">{{ $course->progress_percentage }}%</h3>
                <small class="text-muted">Course Progress</small>
            </div>
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
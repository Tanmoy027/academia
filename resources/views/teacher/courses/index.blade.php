@extends('layouts.bootstrap')

@section('title', 'My Courses')

@section('content')
@php
$totalCourses = $courses->count();
$activeCourses = $courses->filter(fn ($course) => $course->hasStarted() && !$course->hasEnded())->count();
$pendingCourses = $courses->filter(fn ($course) => !$course->hasStarted())->count();
$endedCourses = $courses->filter(fn ($course) => $course->hasEnded())->count();
@endphp

<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 22px; background: linear-gradient(120deg, #5b86e5 0%, #9f7aea 50%, #f472b6 100%);">
    <div class="card-body p-4 p-md-5 text-white">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
            <div>
                <p class="text-uppercase small text-white-50 mb-2">Instructor Workspace</p>
                <h2 class="fw-bold mb-2">Manage {{ $totalCourses }} {{ Str::plural('Course', $totalCourses) }}</h2>
                <p class="text-white-75 mb-3">Stay organised with an overview of enrollment, schedules, and classroom health.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-light text-primary fw-semibold px-4">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('teacher.courses.performance', $courses->first()->id ?? 0) }}" class="btn btn-outline-light px-4 @if($courses->isEmpty()) disabled @endif">
                        <i class="bi bi-graph-up me-2"></i>Performance
                    </a>
                </div>
            </div>
            <div class="text-center">
                <div class="rounded-circle bg-white bg-opacity-25 p-4 d-inline-flex">
                    <i class="bi bi-journal-check text-white" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #36d1dc !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Total</p>
                <h3 class="fw-bold mb-0">{{ $totalCourses }}</h3>
                <span class="text-muted">Courses in library</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #4caf50 !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Live</p>
                <h3 class="fw-bold mb-0">{{ $activeCourses }}</h3>
                <span class="text-muted">Currently running</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #ffb347 !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Upcoming</p>
                <h3 class="fw-bold mb-0">{{ $pendingCourses }}</h3>
                <span class="text-muted">Starting soon</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #f5576c !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Completed</p>
                <h3 class="fw-bold mb-0">{{ $endedCourses }}</h3>
                <span class="text-muted">Recently closed</span>
            </div>
        </div>
    </div>
</div>

@if($courses->count() > 0)
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    @foreach($courses as $course)
    @php
    $progress = $course->progress_percentage ?? 0;
    $status = !$course->is_active ? 'Inactive' : ($course->hasEnded() ? 'Ended' : ($course->hasStarted() ? 'In Progress' : 'Starting Soon'));
    $statusClass = match($status) {
    'Inactive' => 'bg-secondary',
    'Ended' => 'bg-danger',
    'In Progress' => 'bg-success',
    default => 'bg-info text-dark',
    };
    @endphp
    <div class="col">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: 18px;">
            <div class="position-relative" style="height: 160px; border-radius: 18px 18px 0 0; overflow: hidden;">
                @if($course->image_url)
                <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-100 h-100" style="object-fit: cover;">
                @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-book text-white" style="font-size: 2.5rem;"></i>
                </div>
                @endif
                <span class="badge {{ $statusClass }} position-absolute top-0 start-0 m-3">{{ $status }}</span>
                <span class="badge bg-dark bg-opacity-50 text-white position-absolute top-0 end-0 m-3">{{ strtoupper($course->class_type_label) }}</span>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-1">{{ Str::limit($course->title, 40) }}</h5>
                <p class="text-muted small mb-3">{{ Str::limit($course->description, 90) }}</p>
                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                    <span><i class="bi bi-people me-1"></i>{{ $course->students->count() }} students</span>
                    @if($course->schedule)
                    <span><i class="bi bi-calendar-event me-1"></i>{{ $course->schedule }}</span>
                    @endif
                    <span><i class="bi bi-currency-dollar me-1"></i>{{ number_format($course->price, 2) }}</span>
                </div>
                @if($course->start_date && $course->end_date)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Progress</small>
                        <small class="fw-semibold text-primary">{{ $progress }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ $progress }}%; background: linear-gradient(90deg, #6a5be2, #ff8fb1);"></div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Overview
                    </a>
                    <a href="{{ route('teacher.courses.students', $course->id) }}" class="btn btn-outline-success btn-sm rounded-pill">
                        <i class="bi bi-people me-1"></i>Students
                    </a>
                    <a href="{{ route('teacher.courses.performance', $course->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-graph-up-arrow me-1"></i>Performance
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card border-0 shadow-sm" style="border-radius: 18px;">
    <div class="card-body text-center py-5">
        <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3 text-muted">No Courses Assigned</h5>
        <p class="text-muted mb-0">Once an administrator assigns a class, it will appear here.</p>
    </div>
</div>
@endif
@endsection
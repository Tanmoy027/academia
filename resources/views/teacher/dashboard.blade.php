@extends('layouts.bootstrap')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        Teacher Dashboard
    </div>
    <div class="card-body">
        <h5 class="card-title">Welcome {{ Auth::user()->name }}!</h5>
        <p class="card-text">Manage your classes, students, and grades here.</p>
        <a href="{{ route('teacher.courses') }}" class="btn btn-success">
            <i class="bi bi-book"></i> View My Courses
        </a>
    </div>
</div>

<!-- My Courses Section -->
@if(isset($courses) && $courses->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-book"></i> My Courses</span>
        <a href="{{ route('teacher.courses') }}" class="btn btn-sm btn-light">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($courses->take(5) as $course)
            <a href="{{ route('teacher.courses.show', $course->id) }}" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            @php
                            $typeColors = [
                            'online' => 'bg-info',
                            'offline' => 'bg-warning text-dark',
                            'hybrid' => 'bg-purple text-white',
                            ];
                            $badgeClass = $typeColors[$course->class_type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }} me-2">{{ $course->class_type_label }}</span>
                            <h6 class="mb-0 fw-bold">{{ $course->title }}</h6>
                        </div>
                        <div class="d-flex align-items-center mt-2">
                            <span class="text-muted small me-3">
                                <i class="bi bi-people"></i> {{ $course->students->count() }} students
                            </span>
                            @if($course->start_date && $course->end_date)
                            <div class="flex-grow-1" style="max-width: 150px;">
                                <div class="progress" style="height: 6px;">
                                    @php
                                    $progress = $course->progress_percentage;
                                    $progressClass = 'bg-success';
                                    if ($progress < 25) $progressClass='bg-info' ;
                                        elseif ($progress < 50) $progressClass='bg-primary' ;
                                        elseif ($progress < 75) $progressClass='bg-warning' ;
                                        @endphp
                                        <div class="progress-bar {{ $progressClass }}" style="width: {{ $progress }}%">
                                </div>
                            </div>
                        </div>
                        <small class="text-muted ms-2">{{ $progress }}%</small>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    @if(!$course->is_active)
                    <span class="badge bg-secondary">Inactive</span>
                    @elseif($course->hasEnded())
                    <span class="badge bg-danger">Ended</span>
                    @elseif($course->hasStarted())
                    <span class="badge bg-success">In Progress</span>
                    @else
                    <span class="badge bg-primary">Starting Soon</span>
                    @endif
                </div>
        </div>
        </a>
        @endforeach
    </div>
</div>
</div>
@elseif(isset($courses))
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-book"></i> My Courses
    </div>
    <div class="card-body text-center py-4">
        <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3 text-muted">No Courses Assigned</h5>
        <p class="text-muted mb-0">You don't have any courses assigned to you yet.</p>
    </div>
</div>
@endif

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>

<!-- Notices & Announcements Section -->
@if($notices->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <span><i class="bi bi-megaphone"></i> Notices & Announcements</span>
        <a href="{{ route('teacher.notices') }}" class="btn btn-sm btn-outline-dark">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($notices as $notice)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            @php
                            $categoryColors = [
                            'urgent' => 'bg-danger',
                            'exam' => 'bg-warning text-dark',
                            'holiday' => 'bg-success',
                            'class_update' => 'bg-info',
                            'event' => 'bg-primary',
                            'general' => 'bg-secondary',
                            ];
                            $badgeClass = $categoryColors[$notice->category] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }} me-2">{{ $notice->category_label }}</span>
                            <h6 class="mb-0 fw-bold">{{ $notice->title }}</h6>
                        </div>
                        <p class="mb-1 text-muted small">{{ Str::limit($notice->content, 150) }}</p>
                        <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
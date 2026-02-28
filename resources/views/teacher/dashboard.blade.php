@extends('layouts.bootstrap')

@section('title', 'Teacher Dashboard')

@section('content')
@php
$totalCourses = $courses->count();
$totalStudents = $courses->sum(fn ($course) => $course->students->count());
$activeCourses = $courses->filter(fn ($course) => $course->hasStarted() && !$course->hasEnded())->count();
$upcomingCourses = $courses->filter(fn ($course) => !$course->hasStarted())->count();
@endphp

<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 22px; background: linear-gradient(135deg, #6a5be2 0%, #8c4de7 50%, #ff8fb1 100%);">
    <div class="card-body p-4 p-md-5 text-white">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div>
                <p class="text-white-50 text-uppercase small mb-2">Teacher Dashboard</p>
                <h2 class="fw-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="mb-3 text-white-75">Stay on top of your classes, materials, and student performance with a single glance.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('teacher.courses') }}" class="btn btn-light text-primary fw-semibold px-4">
                        <i class="bi bi-journal-text me-2"></i>View Courses
                    </a>
                    <a href="{{ route('teacher.profile') }}" class="btn btn-outline-light px-4">
                        <i class="bi bi-person-lines-fill me-2"></i>Profile
                    </a>
                    <a href="{{ route('teacher.salary') }}" class="btn btn-outline-warning text-white border-warning px-4">
                        <i class="bi bi-wallet2 me-2"></i>Salary
                    </a>
                </div>
            </div>
            <div class="text-center text-lg-end">
                <div class="rounded-circle bg-white bg-opacity-25 p-4 d-inline-flex">
                    <i class="bi bi-easel text-white" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #36d1dc !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Total Courses</p>
                <h3 class="fw-bold mb-0">{{ $totalCourses }}</h3>
                <span class="text-muted">{{ $activeCourses }} live now</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #a8e063 !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Students</p>
                <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                <span class="text-muted">Across all classes</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #f6d365 !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Upcoming</p>
                <h3 class="fw-bold mb-0">{{ $upcomingCourses }}</h3>
                <span class="text-muted">Starting soon</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; border-left: 5px solid #f5576c !important;">
            <div class="card-body">
                <p class="text-uppercase small text-muted mb-1">Announcements</p>
                <h3 class="fw-bold mb-0">{{ $notices->count() }}</h3>
                <span class="text-muted">Latest updates</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 18px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 18px 18px 0 0;">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-journal-richtext text-primary me-2"></i>My Courses</h5>
                    <small class="text-muted">Recent classes with live metrics</small>
                </div>
                <a href="{{ route('teacher.courses') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                    View all
                </a>
            </div>
            <div class="card-body p-0">
                @if($courses->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($courses->take(5) as $course)
                    @php
                    $progress = $course->progress_percentage ?? 0;
                    $performance = isset($coursePerformance) ? $coursePerformance->get($course->id) : null;
                    @endphp
                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="list-group-item list-group-item-action py-4 px-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light text-dark border">{{ strtoupper($course->class_type_label) }}</span>
                                    <h6 class="mb-0 fw-semibold">{{ $course->title }}</h6>
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span><i class="bi bi-people me-1"></i>{{ $course->students->count() }} students</span>
                                    <span><i class="bi bi-calendar-event me-1"></i>{{ $course->schedule ?? 'Flexible' }}</span>
                                    @if($course->start_date)
                                    <span><i class="bi bi-clock-history me-1"></i>{{ $course->start_date->format('M d') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                @if($course->start_date && $course->end_date)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Progress</small>
                                    <small class="fw-semibold text-primary">{{ $progress }}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-gradient" style="width: {{ $progress }}%; background: linear-gradient(90deg, #6a5be2, #ff8fb1);"></div>
                                </div>
                                @endif
                            </div>
                            <div class="text-md-end">
                                <div class="mb-2">
                                    @if(!$course->is_active)
                                    <span class="badge bg-secondary">Inactive</span>
                                    @elseif($course->hasEnded())
                                    <span class="badge bg-danger">Ended</span>
                                    @elseif($course->hasStarted())
                                    <span class="badge bg-success">In Progress</span>
                                    @else
                                    <span class="badge bg-info text-dark">Starting Soon</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span><i class="bi bi-speedometer2 text-success me-1"></i>{{ $performance && $performance['average_score'] !== null ? $performance['average_score'] . '%' : '—' }}</span>
                                    <span><i class="bi bi-calendar-check text-info me-1"></i>{{ $performance && $performance['average_attendance'] !== null ? $performance['average_attendance'] . '%' : '—' }}</span>
                                    <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>{{ $performance ? $performance['at_risk'] : 0 }} at risk</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No courses assigned yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
            <div class="card-header bg-white border-0" style="border-radius: 18px 18px 0 0;">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('teacher.courses') }}" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-folder2-open me-2"></i>Manage Courses
                    </a>
                    <a href="{{ route('teacher.notices') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-megaphone me-2"></i>Announcements
                    </a>
                    <a href="{{ route('teacher.salary') }}" class="btn btn-outline-success rounded-pill">
                        <i class="bi bi-cash-stack me-2"></i>Salary History
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 18px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 18px 18px 0 0;">
                <h6 class="mb-0 fw-bold"><i class="bi bi-megaphone text-danger me-2"></i>Notices</h6>
                <a href="{{ route('teacher.notices') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View all</a>
            </div>
            <div class="card-body p-0">
                @if($notices->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notices as $notice)
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
                    <div class="list-group-item border-0 border-bottom px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                <i class="bi bi-broadcast text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1 fw-semibold">{{ Str::limit($notice->title, 40) }}</h6>
                                    <span class="badge {{ $badgeClass }}">{{ $notice->category_label }}</span>
                                </div>
                                <p class="text-muted small mb-1">{{ Str::limit($notice->content, 80) }}</p>
                                <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-text text-muted" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mt-3 mb-0">No announcements yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
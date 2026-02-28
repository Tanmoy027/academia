@extends('layouts.bootstrap')

@section('title', 'Student Dashboard')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
$nextClass = $upcomingClasses->first();
$firstEnrollment = $enrolledCourses->first();
$firstCourseId = ($firstEnrollment && $firstEnrollment->course) ? $firstEnrollment->course->id : null;
@endphp

<!-- Welcome Banner -->
<div class="card border-0 mb-4 overflow-hidden" style="background: linear-gradient(120deg, #4facfe 0%, #6b8bff 40%, #a18cd1 100%); border-radius: 24px;">
    <div class="card-body p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-light text-primary fw-semibold rounded-pill mb-3">Student Workspace</span>
                <h1 class="text-white fw-bold mb-2">Welcome back, {{ Auth::user()->name }} 👋</h1>
                <p class="text-white-75 mb-4">Plan your week, stay on top of classes, and keep your learning momentum without digging through menus.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('courses.index') }}" class="btn btn-light text-primary fw-semibold rounded-pill px-4">
                        <i class="bi bi-journal-plus me-2"></i>Browse Courses
                    </a>
                    <a href="{{ route('student.notices') }}" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-megaphone me-2"></i>Latest Notices
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4">
                    <div>
                        <p class="text-white-50 text-uppercase small mb-1">Enrolled</p>
                        <h3 class="text-white fw-bold mb-0">{{ $stats['total_enrolled'] }}</h3>
                    </div>
                    <div>
                        <p class="text-white-50 text-uppercase small mb-1">Completed</p>
                        <h3 class="text-white fw-bold mb-0">{{ $stats['completed'] }}</h3>
                    </div>
                    <div>
                        <p class="text-white-50 text-uppercase small mb-1">In Progress</p>
                        <h3 class="text-white fw-bold mb-0">{{ $stats['in_progress'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-white bg-opacity-10 border border-light border-opacity-25 rounded-4 p-4 shadow-sm text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="text-uppercase small mb-1 text-white-50">Next up</p>
                            <h5 class="fw-bold mb-0">{{ $nextClass['course']->title ?? 'No class scheduled' }}</h5>
                        </div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill">Schedule</span>
                    </div>
                    @if($nextClass)
                    <p class="mb-0"><i class="bi bi-clock me-2"></i>{{ $nextClass['schedule'] }}</p>
                    <small class="text-white-50">Tap below to jump into the classroom.</small>
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('courses.show', $nextClass['course']->id) }}" class="btn btn-light btn-sm text-primary rounded-pill flex-grow-1">
                            <i class="bi bi-play-fill me-2"></i>Go to class
                        </a>
                    </div>
                    @else
                    <p class="mb-2 text-white-75">You're all caught up. Use this time to revise or explore new classes.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-light btn-sm text-primary rounded-pill">
                        Discover courses
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Pills -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        @if($firstCourseId)
        <a href="{{ route('student.courses.performance', $firstCourseId) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 quick-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-pill bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Performance</p>
                        <h6 class="mb-0">View course analytics</h6>
                    </div>
                </div>
            </div>
        </a>
        @else
        <div class="card border-0 shadow-sm h-100 quick-card opacity-75">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-pill bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase mb-1">Performance</p>
                    <h6 class="mb-0">Enroll to unlock stats</h6>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4">
        <a href="{{ route('student.notices') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 quick-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-pill bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Announcements</p>
                        <h6 class="mb-0">Catch up on news</h6>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('profile.edit') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 quick-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-pill bg-success bg-opacity-10 text-success">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Profile</p>
                        <h6 class="mb-0">Update preferences</h6>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column - Main Content -->
    <div class="col-lg-8">

        <!-- Learning Timeline -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4 rounded-top-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-week text-primary me-2"></i>Learning Timeline</h5>
                <small class="text-muted">Upcoming {{ $upcomingClasses->count() }} classes</small>
            </div>
            <div class="card-body p-4">
                @if($upcomingClasses->count() > 0)
                <div class="timeline">
                    @foreach($upcomingClasses as $item)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1">{{ $item['course']->title }}</h6>
                                <span class="badge bg-light text-primary rounded-pill">Class</span>
                            </div>
                            <p class="text-muted small mb-2"><i class="bi bi-clock me-1"></i>{{ $item['schedule'] }}</p>
                            <a href="{{ route('courses.show', $item['course']->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">Open classroom</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No scheduled classes yet—plan ahead by enrolling in a course.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4">Find a course</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Active Courses -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header bg-white border-0 rounded-top-4 d-flex justify-content-between align-items-center py-3 px-4">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-bookmark-star text-success me-2"></i>Active Courses</h5>
                    <small class="text-muted">Continue right where you left off</small>
                </div>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">Browse more</a>
            </div>
            <div class="card-body p-4">
                @if($enrolledCourses->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    @foreach($enrolledCourses->take(4) as $enrollment)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm course-card">
                            @if($enrollment->course->image_url)
                            <div class="course-thumb">
                                <img src="{{ $enrollment->course->image_url }}" alt="{{ $enrollment->course->title }}" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            @else
                            <div class="course-thumb placeholder">
                                <i class="bi bi-book text-white fs-2"></i>
                            </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ Str::limit($enrollment->course->title, 28) }}</h6>
                                    <span class="badge {{ $enrollment->course->hasEnded() ? 'bg-success' : ($enrollment->course->hasStarted() ? 'bg-primary' : 'bg-warning text-dark') }}">
                                        {{ $enrollment->course->getStatusLabel() }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-3">{{ Str::limit($enrollment->course->description, 70) }}</p>
                                @if($enrollment->course->start_date && $enrollment->course->end_date)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Progress</span>
                                        <span class="fw-semibold text-primary">{{ $enrollment->course->getProgressPercentage() }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: {{ $enrollment->course->getProgressPercentage() }}%; background: linear-gradient(90deg, #6a5be2, #f699cd);"></div>
                                    </div>
                                </div>
                                @endif
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('courses.show', $enrollment->course->id) }}" class="btn btn-primary btn-sm rounded-pill flex-grow-1">
                                        {{ $enrollment->course->hasEnded() ? 'Review' : 'Continue' }}
                                    </a>
                                    <a href="{{ route('student.courses.performance', $enrollment->course->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="bi bi-graph-up-arrow me-1"></i>Stats
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">You haven't enrolled in anything yet.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4">Start learning</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Discover Courses -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 rounded-top-4 d-flex justify-content-between align-items-center py-3 px-4">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-compass text-info me-2"></i>Discover More</h5>
                    <small class="text-muted">Handpicked picks to keep you inspired</small>
                </div>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">View all</a>
            </div>
            <div class="card-body p-4">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    @forelse($courses->take(6) as $course)
                    @php $isEnrolled = $course->enrollments()->where('user_id', Auth::id())->exists(); @endphp
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm discover-card">
                            @if($course->image_url)
                            <div class="discover-thumb">
                                <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            @else
                            <div class="discover-thumb placeholder">
                                <i class="bi bi-journal-code text-white fs-2"></i>
                            </div>
                            @endif
                            <div class="card-body">
                                <h6 class="fw-semibold mb-1">{{ Str::limit($course->title, 32) }}</h6>
                                <p class="text-muted small mb-3">{{ Str::limit($course->description, 70) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success bg-opacity-10 text-success">৳{{ number_format($course->price, 0) }}</span>
                                    @if($isEnrolled)
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-check-circle me-1"></i>Enrolled</span>
                                    @else
                                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">View</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No courses available right now.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="col-lg-4">

        <!-- Profile Card -->
        <div class="card border-0 shadow-sm mb-4 rounded-4 text-center" style="background: linear-gradient(160deg, #5eead4 0%, #4f46e5 60%, #7c3aed 100%);">
            <div class="card-body p-4 position-relative overflow-hidden">
                <div class="floating-badge">Student</div>
                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-white bg-opacity-25" style="width: 90px; height: 90px;">
                    <span class="text-white fw-bold" style="font-size: 2.4rem;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <h5 class="text-white fw-bold mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-white-75 small mb-3"><i class="bi bi-envelope me-1"></i>{{ Auth::user()->email }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit profile
                </a>
            </div>
        </div>

        @if(isset($performanceInsights) && $performanceInsights->count() > 0)
        @if(isset($performanceInsights) && $performanceInsights->count() > 0)
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header bg-white border-0 rounded-top-4 d-flex justify-content-between align-items-center py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up text-success me-2"></i>Performance Snapshot</h5>
                <small class="text-muted">Latest metrics</small>
            </div>
            <div class="card-body p-0">
                @foreach($performanceInsights->take(3) as $insight)
                @php
                $course = $insight['course'];
                $stateColors = [
                'excellent' => 'success',
                'on-track' => 'primary',
                'warning' => 'warning',
                'risk' => 'danger',
                ];
                $badgeClass = $stateColors[$insight['score']['state']] ?? 'secondary';
                @endphp
                <div class="p-4 border-bottom">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <h6 class="fw-semibold mb-1">{{ Str::limit($course->title, 30) }}</h6>
                            <small class="text-muted">Attendance {{ $insight['attendance']['rate'] !== null ? $insight['attendance']['rate'] . '%' : '—' }} • Assignments {{ $insight['assignments']['completion_rate'] !== null ? $insight['assignments']['completion_rate'] . '%' : '—' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $badgeClass }} mb-1">{{ $insight['score']['label'] }}</span>
                            <div class="fw-bold">{{ $insight['score']['overall'] }}%</div>
                        </div>
                    </div>
                    <a href="{{ route('student.courses.performance', $course->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">View details</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        <!-- Notices & Announcements Section -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header bg-white border-0 rounded-top-4 d-flex justify-content-between align-items-center py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-megaphone text-warning me-2"></i>Announcements</h5>
                <a href="{{ route('student.notices') }}" class="btn btn-outline-warning btn-sm rounded-pill">View all</a>
            </div>
            <div class="card-body p-0">
                @if($notices->count() > 0)
                @foreach($notices as $notice)
                @php
                $categoryIcons = [
                'urgent' => 'bi-exclamation-triangle-fill text-danger',
                'exam' => 'bi-pencil-square text-warning',
                'holiday' => 'bi-calendar-heart text-success',
                'class_update' => 'bi-info-circle text-info',
                'event' => 'bi-star text-primary',
                'general' => 'bi-bell text-secondary',
                ];
                $iconClass = $categoryIcons[$notice->category] ?? 'bi-bell text-secondary';
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
                <div class="px-4 py-3 border-bottom">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi {{ $iconClass }} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-semibold mb-0">{{ Str::limit($notice->title, 34) }}</h6>
                                <span class="badge {{ $badgeClass }} rounded-pill" style="font-size: 0.7rem;">{{ $notice->category_label }}</span>
                            </div>
                            <p class="text-muted small mb-1">{{ Str::limit($notice->content, 70) }}</p>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No announcements at the moment</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tasks & Assignments Section (Placeholder for future) -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-task text-danger me-2"></i>Tasks & Assignments</h5>
            </div>
            <div class="card-body p-4">
                <div class="text-center py-4">
                    <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No pending tasks—you're caught up!</p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-lightning-charge text-purple me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('courses.index') }}" class="quick-link">
                        <i class="bi bi-book"></i>
                        <div>
                            <strong>Browse all courses</strong>
                            <p>Discover new topics and enroll instantly.</p>
                        </div>
                        <i class="bi bi-arrow-right-short ms-auto"></i>
                    </a>
                    <a href="{{ route('student.notices') }}" class="quick-link">
                        <i class="bi bi-bell"></i>
                        <div>
                            <strong>All announcements</strong>
                            <p>Never miss an update from your instructors.</p>
                        </div>
                        <i class="bi bi-arrow-right-short ms-auto"></i>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="quick-link">
                        <i class="bi bi-gear"></i>
                        <div>
                            <strong>Account settings</strong>
                            <p>Manage your profile and security.</p>
                        </div>
                        <i class="bi bi-arrow-right-short ms-auto"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
    }

    .quick-card {
        border-radius: 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .quick-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.12) !important;
    }

    .icon-pill {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .course-card,
    .discover-card {
        border-radius: 18px;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .course-card:hover,
    .discover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12) !important;
    }

    .course-thumb,
    .discover-thumb {
        height: 140px;
        overflow: hidden;
    }

    .course-thumb.placeholder,
    .discover-thumb.placeholder {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .floating-badge {
        position: absolute;
        top: 18px;
        right: 18px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        padding: 0.35rem 1rem;
        font-weight: 600;
        color: #fff;
        font-size: 0.85rem;
    }

    .timeline {
        position: relative;
        margin-left: 0.8rem;
    }

    .timeline:before {
        content: '';
        position: absolute;
        left: 12px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: linear-gradient(180deg, #cbd5f5, #e0e7ff);
    }

    .timeline-item {
        position: relative;
        padding-left: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .timeline-dot {
        position: absolute;
        left: 3px;
        top: 6px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #6366f1;
    }

    .timeline-content {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1rem 1.25rem;
    }

    .quick-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1rem;
        border-radius: 16px;
        text-decoration: none;
        color: inherit;
        background: #f8fafc;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .quick-link i {
        font-size: 1.3rem;
        color: #6366f1;
    }

    .quick-link p {
        margin: 0;
        font-size: 0.9rem;
        color: #64748b;
    }

    .quick-link:hover {
        background: #eef2ff;
        transform: translateX(4px);
    }
</style>
@endsection
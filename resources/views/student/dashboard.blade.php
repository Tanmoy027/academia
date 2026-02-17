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

<!-- Welcome Banner -->
<div class="card border-0 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="text-white fw-bold mb-2">
                    <i class="bi bi-mortarboard-fill me-2"></i>Welcome back, {{ Auth::user()->name }}!
                </h2>
                <p class="text-white-50 mb-3">Your learning journey continues here. Track your progress and stay updated.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-light btn-lg rounded-pill px-4 shadow-sm">
                    <i class="bi bi-book me-2"></i>Browse Courses
                </a>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <i class="bi bi-person-workspace text-white" style="font-size: 8rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #667eea !important; border-radius: 15px;">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-book-half text-white fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0 text-uppercase fw-semibold">Enrolled Courses</p>
                    <h3 class="fw-bold mb-0">{{ $stats['total_enrolled'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #11998e !important; border-radius: 15px;">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="bi bi-check-circle text-white fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0 text-uppercase fw-semibold">Completed</p>
                    <h3 class="fw-bold mb-0">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f093fb !important; border-radius: 15px;">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-hourglass-split text-white fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0 text-uppercase fw-semibold">In Progress</p>
                    <h3 class="fw-bold mb-0">{{ $stats['in_progress'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column - Main Content -->
    <div class="col-lg-8">

        <!-- Upcoming Classes / Schedule Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-event text-primary me-2"></i>Upcoming Classes & Schedule
                </h5>
            </div>
            <div class="card-body p-4">
                @if($upcomingClasses->count() > 0)
                @foreach($upcomingClasses as $item)
                <div class="d-flex align-items-center p-3 mb-3 rounded-3" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-play-circle text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">{{ $item['course']->title }}</h6>
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-clock me-1"></i>{{ $item['schedule'] }}
                        </p>
                    </div>
                    <a href="{{ route('courses.show', $item['course']->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        View <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                @endforeach
                @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">No upcoming classes scheduled</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-circle me-2"></i>Enroll in a Course
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- My Enrolled Courses Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-bookmark-star text-success me-2"></i>My Enrolled Courses
                </h5>
                <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="bi bi-plus me-1"></i>Browse More
                </a>
            </div>
            <div class="card-body p-4">
                @if($enrolledCourses->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    @foreach($enrolledCourses->take(4) as $enrollment)
                    <div class="col">
                        <div class="card h-100 border shadow-sm hover-shadow" style="border-radius: 12px; transition: all 0.3s;">
                            <div class="row g-0">
                                <div class="col-4">
                                    @if($enrollment->course->image_url)
                                    <img src="{{ $enrollment->course->image_url }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $enrollment->course->title }}" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center h-100\' style=\'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100px;\'><i class=\'bi bi-book text-white fs-3\'></i></div>';">
                                    @else
                                    <div class="d-flex align-items-center justify-content-center h-100 rounded-start" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100px;">
                                        <i class="bi bi-book text-white fs-3"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-8">
                                    <div class="card-body py-2 px-3">
                                        <h6 class="card-title fw-bold mb-1">{{ Str::limit($enrollment->course->title, 25) }}</h6>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge {{ $enrollment->course->hasEnded() ? 'bg-success' : ($enrollment->course->hasStarted() ? 'bg-primary' : 'bg-warning text-dark') }}">
                                                {{ $enrollment->course->getStatusLabel() }}
                                            </span>
                                        </div>
                                        @if($enrollment->course->start_date && $enrollment->course->end_date)
                                        <!-- Course Progress Bar -->
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Progress</small>
                                                <small class="fw-bold text-primary">{{ $enrollment->course->getProgressPercentage() }}%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $enrollment->course->getProgressPercentage() }}%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                                                    aria-valuenow="{{ $enrollment->course->getProgressPercentage() }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('courses.show', $enrollment->course->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                                {{ $enrollment->course->hasEnded() ? 'View' : 'Continue' }} <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 mb-4">You haven't enrolled in any courses yet</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-search me-2"></i>Explore Courses
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Available Courses to Explore -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-collection text-info me-2"></i>Explore Courses
                </h5>
                <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-4">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    @forelse($courses->take(6) as $course)
                    @php
                    $isEnrolled = $course->enrollments()->where('user_id', Auth::id())->exists();
                    @endphp
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm hover-shadow" style="border-radius: 12px; transition: all 0.3s;">
                            @if($course->image_url)
                            <div style="height: 120px; overflow: hidden; border-radius: 12px 12px 0 0;">
                                <img src="{{ $course->image_url }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $course->title }}" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center h-100\' style=\'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\'><i class=\'bi bi-book text-white fs-2\'></i></div>';">
                            </div>
                            @else
                            <div class="d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;">
                                <i class="bi bi-book text-white fs-2"></i>
                            </div>
                            @endif
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-2">{{ Str::limit($course->title, 30) }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success bg-gradient">৳{{ number_format($course->price, 0) }}</span>
                                    @if($isEnrolled)
                                    <span class="badge bg-primary"><i class="bi bi-check-circle me-1"></i>Enrolled</span>
                                    @else
                                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">No courses available at the moment.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="col-lg-4">

        <!-- Profile Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <span class="text-white fw-bold" style="font-size: 2rem;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted small mb-3">
                    <i class="bi bi-envelope me-1"></i>{{ Auth::user()->email }}
                </p>
                <span class="badge bg-primary bg-gradient rounded-pill px-3 py-2 mb-3">
                    <i class="bi bi-mortarboard me-1"></i>Student
                </span>
                <div class="d-grid">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Notices & Announcements Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 px-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-megaphone text-warning me-2"></i>Announcements
                </h5>
                <a href="{{ route('student.notices') }}" class="btn btn-sm btn-outline-warning rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                @if($notices->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notices as $notice)
                    <div class="list-group-item border-0 px-4 py-3">
                        <div class="d-flex align-items-start">
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
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 40px; height: 40px;">
                                    <i class="bi {{ $iconClass }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-semibold">{{ Str::limit($notice->title, 30) }}</h6>
                                    <span class="badge {{ $badgeClass }} rounded-pill" style="font-size: 0.65rem;">{{ $notice->category_label }}</span>
                                </div>
                                <p class="mb-1 text-muted small">{{ Str::limit($notice->content, 60) }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 px-4">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No announcements at the moment</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tasks & Assignments Section (Placeholder for future) -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-task text-danger me-2"></i>Tasks & Assignments
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="text-center py-4">
                    <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No pending tasks or assignments</p>
                </div>
                <!-- Sample tasks structure for when data is available:
                <div class="d-flex align-items-center p-2 mb-2 rounded bg-light">
                    <div class="form-check me-2">
                        <input class="form-check-input" type="checkbox">
                    </div>
                    <div class="flex-grow-1">
                        <small class="fw-semibold">Complete Quiz 1</small>
                        <div class="text-muted" style="font-size: 0.7rem;">Due: Feb 20, 2026</div>
                    </div>
                    <span class="badge bg-danger rounded-pill">Due Soon</span>
                </div>
                -->
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-lightning text-purple me-2"></i>Quick Links
                </h5>
            </div>
            <div class="card-body p-2">
                <div class="list-group list-group-flush">
                    <a href="{{ route('courses.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 d-flex align-items-center">
                        <i class="bi bi-book me-3 text-primary"></i>
                        <span>Browse All Courses</span>
                    </a>
                    <a href="{{ route('student.notices') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 d-flex align-items-center">
                        <i class="bi bi-bell me-3 text-warning"></i>
                        <span>All Announcements</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 d-flex align-items-center">
                        <i class="bi bi-person-gear me-3 text-info"></i>
                        <span>Account Settings</span>
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
</style>
@endsection
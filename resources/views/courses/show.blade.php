@extends('layouts.bootstrap')

@section('title', $course->title)

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

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none"><i class="bi bi-house me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none">Courses</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($course->title, 30) }}</li>
    </ol>
</nav>

<div class="row g-5">
    <!-- Course Content -->
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 mb-4">
            @if($course->image_url)
            <div class="position-relative overflow-hidden" style="max-height: 400px;">
                <img src="{{ $course->image_url }}" class="w-100" alt="{{ $course->title }}" style="object-fit: cover;" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center py-5 bg-light\'><i class=\'bi bi-image text-muted\' style=\'font-size: 5rem;\'></i></div>';">
                <div class="position-absolute bottom-0 start-0 end-0 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                    <h1 class="text-white fw-bold mb-0">{{ $course->title }}</h1>
                </div>
            </div>
            @else
            <div class="card-header py-4">
                <h1 class="text-white fw-bold mb-0">{{ $course->title }}</h1>
            </div>
            @endif

            <div class="card-body p-4">
                <div class="d-flex flex-wrap gap-3 mb-4">
                    @if($course->duration)
                    <span class="badge bg-primary bg-gradient px-3 py-2">
                        <i class="bi bi-clock me-1"></i>{{ $course->duration }}
                    </span>
                    @endif
                    @if($course->schedule)
                    <span class="badge bg-info bg-gradient px-3 py-2">
                        <i class="bi bi-calendar me-1"></i>{{ $course->schedule }}
                    </span>
                    @endif
                    @if($course->teacher)
                    <span class="badge bg-secondary bg-gradient px-3 py-2">
                        <i class="bi bi-person me-1"></i>{{ $course->teacher->name }}
                    </span>
                    @endif
                    @if($course->start_date)
                    <span class="badge {{ $course->hasStarted() ? 'bg-warning text-dark' : 'bg-success' }} bg-gradient px-3 py-2">
                        <i class="bi bi-calendar-check me-1"></i>
                        {{ $course->hasStarted() ? 'Started' : 'Starts' }}: {{ $course->start_date->format('M d, Y') }}
                    </span>
                    @endif
                    @if($course->end_date)
                    <span class="badge bg-danger bg-gradient px-3 py-2">
                        <i class="bi bi-calendar-x me-1"></i>Ends: {{ $course->end_date->format('M d, Y') }}
                    </span>
                    @endif
                </div>

                @if($course->hasStarted() && !$course->hasEnded() && $course->start_date && $course->end_date)
                <!-- Course Progress Bar -->
                <div class="mb-4 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-muted">Course Progress</span>
                        <span class="badge bg-primary">{{ $course->getProgressPercentage() }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-gradient" role="progressbar"
                            style="width: {{ $course->getProgressPercentage() }}%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                            aria-valuenow="{{ $course->getProgressPercentage() }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                @endif

                <h4 class="fw-bold mb-3">
                    <i class="bi bi-file-text me-2 text-primary"></i>Course Description
                </h4>
                <p class="text-muted lead">{{ $course->description }}</p>
            </div>
        </div>

        <!-- Course Details Card -->
        <div class="card shadow border-0">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-check me-2 text-primary"></i>Course Details
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                        <span class="text-muted"><i class="bi bi-hourglass-split me-2"></i>Duration</span>
                        <span class="fw-semibold">{{ $course->duration ?? 'Not specified' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                        <span class="text-muted"><i class="bi bi-calendar-event me-2"></i>Schedule</span>
                        <span class="fw-semibold">{{ $course->schedule ?? 'Flexible' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                        <span class="text-muted"><i class="bi bi-calendar-check me-2"></i>Start Date</span>
                        <span class="fw-semibold">{{ $course->start_date ? $course->start_date->format('M d, Y') : 'To be announced' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                        <span class="text-muted"><i class="bi bi-calendar-x me-2"></i>End Date</span>
                        <span class="fw-semibold">{{ $course->end_date ? $course->end_date->format('M d, Y') : 'To be announced' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                        <span class="text-muted"><i class="bi bi-person-badge me-2"></i>Instructor</span>
                        <span class="fw-semibold">{{ $course->teacher->name ?? 'To be announced' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Sidebar - Enrollment Card -->
    <div class="col-lg-4">
        <div class="card shadow-lg border-0 sticky-top" style="top: 2rem;">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <span class="text-muted small">Course Price</span>
                    <h2 class="display-5 fw-bold mb-0" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        ৳{{ number_format($course->price, 2) }}
                    </h2>
                </div>

                <hr class="my-4">

                @auth
                @if($isEnrolled)
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Enrolled!</strong><br>
                        <small>You have access to this course.</small>
                    </div>
                </div>
                <button class="btn btn-secondary btn-lg w-100" disabled>
                    <i class="bi bi-check2-circle me-2"></i>Already Enrolled
                </button>
                @elseif(!$canEnroll)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Enrollment Closed</strong><br>
                        <small>This course has already started. Enrollment is no longer available.</small>
                    </div>
                </div>
                <button class="btn btn-secondary btn-lg w-100" disabled>
                    <i class="bi bi-x-circle me-2"></i>Enrollment Closed
                </button>
                @else
                <a href="{{ route('payment.checkout', $course->id) }}" class="btn btn-success btn-lg w-100 mb-3">
                    <i class="bi bi-cart-plus me-2"></i>Enroll Now - ৳{{ number_format($course->price, 2) }}
                </a>
                @if($course->start_date)
                <div class="text-center mb-2">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>Enrollment closes on {{ $course->start_date->format('M d, Y') }}
                    </small>
                </div>
                @endif
                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>Secure payment via Stripe
                    </small>
                </div>
                @endif
                @else
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>Please login to enroll in this course.</div>
                </div>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login to Enroll
                </a>
                <p class="text-center text-muted small mb-0">
                    Don't have an account? <a href="{{ route('register') }}">Register</a>
                </p>
                @endauth
            </div>

            <!-- Features -->
            <div class="card-footer bg-light p-4">
                <h6 class="fw-bold mb-3">This course includes:</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="bi bi-play-circle text-primary me-2"></i>Video lectures</li>
                    <li class="mb-2"><i class="bi bi-file-earmark-text text-primary me-2"></i>Study materials</li>
                    <li class="mb-2"><i class="bi bi-infinity text-primary me-2"></i>Lifetime access</li>
                    <li class="mb-0"><i class="bi bi-patch-check text-primary me-2"></i>Certificate</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
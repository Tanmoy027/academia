@extends('layouts.bootstrap')

@section('title', 'Student Dashboard')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        Student Dashboard
    </div>
    <div class="card-body">
        <h5 class="card-title">Welcome {{ Auth::user()->name }}!</h5>
        <p class="card-text">View available courses and announcements below.</p>
    </div>
</div>

<!-- Notices & Announcements Section -->
@if($notices->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <span><i class="bi bi-megaphone"></i> Notices & Announcements</span>
        <a href="{{ route('student.notices') }}" class="btn btn-sm btn-outline-dark">View All</a>
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

<h3 class="mb-3">Available Courses</h3>
<div class="row row-cols-1 row-cols-md-3 g-4">
    @forelse($courses as $course)
    <div class="col">
        <div class="card h-100">
            @if($course->image_url)
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-2" style="min-height: 180px;">
                <img src="{{ $course->image_url }}" class="img-fluid" alt="{{ $course->title }}" style="max-height: 200px; width: auto; object-fit: contain;" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-muted\'>No Image</span>';">
            </div>
            @else
            <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="min-height: 180px;">
                <span>No Image</span>
            </div>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $course->title }}</h5>
                <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                <p class="card-text"><strong>Price:</strong> ৳{{ number_format($course->price, 2) }}</p>
            </div>
            <div class="card-footer">
                @php
                $isEnrolled = $course->enrollments()->where('user_id', Auth::id())->exists();
                @endphp

                @if($isEnrolled)
                <button class="btn btn-success w-100" disabled>Enrolled</button>
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary w-100 mt-2">View Course</a>
                @else
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary w-100">View & Enroll</a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">No courses available at the moment.</div>
    </div>
    @endforelse
</div>
@endsection
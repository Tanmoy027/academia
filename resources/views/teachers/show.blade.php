@extends('layouts.bootstrap')

@section('title', $teacher->name)

@php use Illuminate\Support\Str; @endphp

@push('styles')
<style>
    .profile-banner {
        background: linear-gradient(120deg, #1d2671, #c33764);
        border-radius: 30px;
        padding: 3rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(29, 38, 113, 0.35);
        margin-bottom: 2.5rem;
    }

    .profile-banner::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.25), transparent 50%);
        pointer-events: none;
    }

    .profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 30px;
        object-fit: cover;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    }

    .profile-stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 20px 50px rgba(29, 38, 113, 0.12);
        text-align: center;
    }

    .profile-stat-card span {
        display: block;
        font-size: 2rem;
        font-weight: 700;
        color: #1d2671;
    }

    .course-panel {
        background: #fff;
        border-radius: 24px;
        padding: 1.75rem;
        box-shadow: 0 20px 45px rgba(29, 38, 113, 0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-thumb {
        width: 100%;
        height: 180px;
        border-radius: 16px;
        object-fit: cover;
        margin-bottom: 1rem;
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #1d2671;
    }

    .course-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .badge-pill {
        border-radius: 999px;
        padding: 0.35rem 0.9rem;
        background: rgba(29, 38, 113, 0.08);
        color: #1d2671;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="profile-banner">
    <div class="row align-items-center">
        <div class="col-md-3 text-center text-md-start mb-4 mb-md-0">
            @if($teacher->profile_picture_url)
            <img src="{{ $teacher->profile_picture_url }}" alt="{{ $teacher->name }}" class="profile-avatar">
            @else
            <div class="profile-avatar d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2); font-size: 3.5rem;">
                {{ strtoupper(substr($teacher->name, 0, 1)) }}
            </div>
            @endif
        </div>
        <div class="col-md-9">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h1 class="mb-0">{{ $teacher->name }}</h1>
                    @if($teacher->specialization)
                    <p class="lead mb-1">{{ $teacher->specialization }}</p>
                    @endif
                    @if($teacher->qualification)
                    <p class="mb-0 opacity-75">{{ $teacher->qualification }}</p>
                    @endif
                </div>
                @php $social = $teacher->social_links ?? []; @endphp
                <div class="d-flex gap-2">
                    @if(!empty($social['linkedin']))
                    <a href="{{ $social['linkedin'] }}" class="btn btn-light" target="_blank" rel="noopener">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    @endif
                    @if(!empty($social['twitter']))
                    <a href="{{ $social['twitter'] }}" class="btn btn-light" target="_blank" rel="noopener">
                        <i class="bi bi-twitter"></i>
                    </a>
                    @endif
                    @if(!empty($social['facebook']))
                    <a href="{{ $social['facebook'] }}" class="btn btn-light" target="_blank" rel="noopener">
                        <i class="bi bi-facebook"></i>
                    </a>
                    @endif
                    @if(!empty($social['website']))
                    <a href="{{ $social['website'] }}" class="btn btn-light" target="_blank" rel="noopener">
                        <i class="bi bi-globe"></i>
                    </a>
                    @endif
                </div>
            </div>
            @if($teacher->bio)
            <p class="mt-4 fs-5" style="max-width: 720px;">{{ $teacher->bio }}</p>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="profile-stat-card">
            <span>{{ $stats['active_courses'] }}</span>
            Active Courses
        </div>
    </div>
    <div class="col-md-4">
        <div class="profile-stat-card">
            <span>{{ $stats['total_courses'] }}</span>
            Total Courses
        </div>
    </div>
    <div class="col-md-4">
        <div class="profile-stat-card">
            <span>{{ $stats['students_taught'] }}</span>
            Students Taught
        </div>
    </div>
</div>

<h2 class="mb-4">Courses by {{ $teacher->name }}</h2>
<div class="row g-4">
    @forelse($teacher->teachingCourses as $course)
    <div class="col-md-6 col-xl-4">
        <div class="course-panel">
            @if($course->image_url)
            <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="course-thumb" loading="lazy">
            @else
            <div class="course-thumb d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #1d2671, #0f2027);">
                <i class="bi bi-collection-play" style="font-size: 2.5rem;"></i>
            </div>
            @endif

            <h5 class="fw-bold">{{ $course->title }}</h5>
            <p class="text-muted">{{ Str::limit($course->description, 120) }}</p>

            <div class="course-meta mb-3">
                <span><i class="bi bi-currency-dollar"></i>{{ number_format($course->price, 2) }}</span>
                @if($course->start_date)
                <span><i class="bi bi-calendar-event"></i>{{ $course->start_date->format('M d, Y') }}</span>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <span class="badge-pill">{{ $course->getStatusLabel() }}</span>
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary btn-sm">
                    View Course
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            {{ $teacher->name }} has not published any courses yet. Check back soon!
        </div>
    </div>
    @endforelse
</div>
@endsection
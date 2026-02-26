@extends('layouts.bootstrap')

@section('title', 'Meet Our Teachers')

@php use Illuminate\Support\Str; @endphp

@push('styles')
<style>
    .teacher-hero {
        background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
        padding: 4rem 3rem;
        border-radius: 30px;
        color: #f8f9fa;
        box-shadow: 0 20px 60px rgba(15, 32, 39, 0.35);
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .teacher-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.4), transparent 55%);
        pointer-events: none;
    }

    .teacher-hero h1 {
        font-size: 2.75rem;
        font-weight: 700;
    }

    .teacher-search input {
        border-radius: 50px;
        padding: 0.85rem 1.25rem;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }

    .teacher-search input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .teacher-card {
        background: #fff;
        border-radius: 22px;
        padding: 2rem;
        box-shadow: 0 20px 45px rgba(15, 32, 39, 0.12);
        transition: transform 0.35s ease, box-shadow 0.35s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .teacher-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 65px rgba(15, 32, 39, 0.18);
    }

    .teacher-avatar {
        width: 90px;
        height: 90px;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 0 10px 25px rgba(15, 32, 39, 0.2);
    }

    .teacher-stat {
        border-radius: 16px;
        padding: 1rem;
        background: #f8f9fb;
        text-align: center;
    }

    .teacher-stat span {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c5364;
    }

    .course-chip {
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        background: rgba(15, 32, 39, 0.08);
        font-size: 0.85rem;
        margin: 0.15rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: #2c5364;
    }

    .empty-state {
        text-align: center;
        padding: 4rem;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 20px 45px rgba(15, 32, 39, 0.08);
    }

    .btn-view-profile {
        border-radius: 14px;
        padding: 0.8rem 1.5rem;
        background: linear-gradient(135deg, #2c5364, #203a43);
        border: none;
        color: #fff;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 10px 25px rgba(32, 58, 67, 0.25);
        transition: transform 0.3s ease;
    }

    .btn-view-profile:hover {
        transform: translateY(-2px);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="teacher-hero">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <h1 class="mb-3">Meet Our Mentors</h1>
            <p class="mb-4 fs-5 opacity-75">
                Explore the educators who power Academia. Browse their expertise, discover the courses they lead,
                and find the perfect mentor for your learning path.
            </p>
        </div>
        <div class="col-lg-5">
            <form method="GET" action="{{ route('teachers.index') }}" class="teacher-search" autocomplete="off">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        class="form-control text-white shadow-none"
                        placeholder="Search by name or specialization"
                        value="{{ $search }}">
                    @if($search)
                    <a href="{{ route('teachers.index') }}" class="btn btn-light text-primary px-4">Clear</a>
                    @else
                    <button class="btn btn-light text-primary px-4" type="submit">Search</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    @forelse($teachers as $teacher)
    <div class="col-md-6 col-xl-4">
        <div class="teacher-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                @if($teacher->profile_picture_url)
                <img src="{{ $teacher->profile_picture_url }}" alt="{{ $teacher->name }}" class="teacher-avatar">
                @else
                <div class="teacher-avatar d-flex align-items-center justify-content-center bg-gradient" style="background: linear-gradient(135deg, #2c5364, #0f2027); color: #fff; font-size: 2rem;">
                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                </div>
                @endif
                <div>
                    <h5 class="mb-1">{{ $teacher->name }}</h5>
                    @if($teacher->specialization)
                    <span class="badge bg-dark-subtle text-dark">{{ $teacher->specialization }}</span>
                    @else
                    <span class="text-muted small">Specialization not set</span>
                    @endif
                </div>
            </div>

            <div class="row text-center g-3 mb-4">
                <div class="col">
                    <div class="teacher-stat">
                        <span>{{ $teacher->active_courses_count ?? 0 }}</span>
                        Active Courses
                    </div>
                </div>
                <div class="col">
                    <div class="teacher-stat">
                        <span>{{ $teacher->total_courses_count ?? 0 }}</span>
                        Total Courses
                    </div>
                </div>
            </div>

            @if($teacher->bio)
            <p class="text-muted mb-3">{{ Str::limit($teacher->bio, 120) }}</p>
            @else
            <p class="text-muted mb-3">This teacher has not added a bio yet.</p>
            @endif

            @if($teacher->teachingCourses->isNotEmpty())
            <div class="mb-4">
                <p class="fw-semibold text-dark mb-2">Featured Courses</p>
                @foreach($teacher->teachingCourses as $course)
                <span class="course-chip">
                    <i class="bi bi-play-btn"></i>{{ Str::limit($course->title, 28) }}
                </span>
                @endforeach
            </div>
            @endif

            <div class="mt-auto">
                <a href="{{ route('teachers.show', $teacher) }}" class="btn-view-profile w-100">
                    <i class="bi bi-person-lines-fill"></i> View Profile
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state">
            <i class="bi bi-people display-4 text-secondary mb-3"></i>
            <h4>No teachers found</h4>
            <p class="text-muted mb-0">Once instructors join the platform, they will appear here for everyone to explore.</p>
        </div>
    </div>
    @endforelse
</div>

{{ $teachers->links('pagination::bootstrap-5') }}
@endsection
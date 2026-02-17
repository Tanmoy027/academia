@extends('layouts.bootstrap')

@section('title', 'Available Courses')

@push('styles')
<style>
    .courses-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 4rem 2rem;
        margin: -2rem -12px 3rem;
        border-radius: 0 0 50px 50px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .courses-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .courses-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .course-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .course-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.25);
    }

    .course-image-wrapper {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .course-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.5s ease;
    }

    .course-card:hover .course-image {
        transform: scale(1.08);
    }

    .course-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .course-image-placeholder i {
        font-size: 4rem;
        color: rgba(255, 255, 255, 0.4);
    }

    .course-status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .course-content {
        padding: 1.5rem;
    }

    .course-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .course-description {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .course-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .course-price {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .course-duration {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 20px;
    }

    .course-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid #f0f0f0;
    }

    .btn-view-course {
        width: 100%;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-view-course:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
    }

    .empty-state i {
        font-size: 6rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }

    .empty-state h4 {
        color: #6c757d;
        font-weight: 600;
    }

    .empty-state p {
        color: #adb5bd;
    }

    @media (max-width: 768px) {
        .courses-header {
            padding: 3rem 1.5rem;
            margin: -2rem -12px 2rem;
            border-radius: 0 0 30px 30px;
        }

        .courses-header h1 {
            font-size: 1.8rem;
        }

        .course-image-wrapper {
            height: 180px;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="courses-header text-center">
    <h1 class="text-white mb-3">
        <i class="bi bi-book-half me-2"></i>Explore Our Courses
    </h1>
    <p class="text-white mb-0">Discover amazing courses and start your learning journey today</p>
</div>

<div class="container">
    <div class="row g-4 mb-5">
        @forelse($courses as $course)
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="course-card h-100 d-flex flex-column">
                <div class="course-image-wrapper">
                    @if($course->image_url)
                    <img
                        src="{{ $course->image_url }}"
                        class="course-image"
                        alt="{{ $course->title }}"
                        loading="lazy"
                        onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'course-image-placeholder\'><i class=\'bi bi-image\'></i></div>';">
                    @else
                    <div class="course-image-placeholder">
                        <i class="bi bi-book"></i>
                    </div>
                    @endif
                    @if($course->canEnroll())
                    <div class="course-status-badge">
                        <i class="bi bi-check-circle-fill"></i>
                        @if($course->start_date)
                        @if($course->getDaysUntilStart() <= 7)
                            Starts in {{ $course->getDaysUntilStart() }} days
                            @else
                            Enrollment Open
                            @endif
                            @else
                            Available
                            @endif
                            </div>
                            @else
                            <div class="course-status-badge" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); box-shadow: 0 4px 15px rgba(238, 90, 36, 0.4);">
                                <i class="bi bi-x-circle-fill"></i>
                                {{ $course->hasEnded() ? 'Completed' : 'Enrollment Closed' }}
                            </div>
                            @endif
                    </div>

                    <div class="course-content flex-grow-1">
                        <h5 class="course-title">{{ $course->title }}</h5>
                        <p class="course-description">{{ Str::limit($course->description, 100) }}</p>
                        @if($course->start_date)
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                Starts: {{ $course->start_date->format('M d, Y') }}
                            </small>
                        </div>
                        @endif
                        <div class="course-meta">
                            <span class="course-price">
                                ৳{{ number_format($course->price, 2) }}
                            </span>
                            @if($course->duration)
                            <span class="course-duration">
                                <i class="bi bi-clock"></i>
                                {{ $course->duration }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="course-footer">
                        <a href="{{ route('courses.show', $course->id) }}" class="btn-view-course">
                            <i class="bi bi-eye"></i>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-journal-x"></i>
                    <h4>No Courses Available</h4>
                    <p>Check back later for new courses!</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    @endsection
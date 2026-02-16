@extends('layouts.bootstrap')

@section('title', 'Available Courses')

@section('content')
<!-- Page Header -->
<div class="page-header text-center mb-5" style="margin: -2rem -12px 2rem; padding: 3rem 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0 0 30px 30px;">
    <h1 class="display-5 fw-bold text-white mb-2">
        <i class="bi bi-book-half me-2"></i>Explore Our Courses
    </h1>
    <p class="text-white-50 mb-0">Discover amazing courses and start your learning journey today</p>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
    @forelse($courses as $course)
    <div class="col">
        <div class="card course-card h-100">
            @if($course->image_url)
            <div class="position-relative overflow-hidden" style="height: 200px;">
                <img src="{{ $course->image_url }}" class="w-100 h-100" alt="{{ $course->title }}" style="object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center h-100 bg-light\'><i class=\'bi bi-image text-muted\' style=\'font-size: 3rem;\'></i></div>';">
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge bg-success bg-gradient px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-check-circle me-1"></i>Available
                    </span>
                </div>
            </div>
            @else
            <div class="d-flex align-items-center justify-content-center bg-gradient" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-book text-white" style="font-size: 4rem; opacity: 0.5;"></i>
            </div>
            @endif
            <div class="card-body">
                <h5 class="card-title fw-bold mb-2">{{ $course->title }}</h5>
                <p class="card-text text-muted small mb-3">{{ Str::limit($course->description, 100) }}</p>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="price-tag">
                        ৳{{ number_format($course->price, 2) }}
                    </span>
                    @if($course->duration)
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-clock me-1"></i>{{ $course->duration }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-transparent py-3">
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary w-100">
                    <i class="bi bi-eye me-1"></i>View Details
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-journal-x text-muted" style="font-size: 5rem;"></i>
            </div>
            <h4 class="text-muted">No Courses Available</h4>
            <p class="text-muted">Check back later for new courses!</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
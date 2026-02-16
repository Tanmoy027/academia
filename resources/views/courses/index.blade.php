@extends('layouts.bootstrap')

@section('title', 'Available Courses')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Available Courses</h1>

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
                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary w-100">View Details</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No courses available at the moment.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
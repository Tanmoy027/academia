@extends('layouts.bootstrap')

@section('title', $course->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-3">{{ $course->title }}</h1>
            @if($course->image_url)
            <div class="bg-light rounded mb-4 p-3 text-center">
                <img src="{{ $course->image_url }}" class="img-fluid rounded" alt="{{ $course->title }}" style="max-height: 400px; width: auto; object-fit: contain;" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-muted\'>Image not available</span>';">
            </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    Description
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $course->description }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    Course Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Duration:</strong> {{ $course->duration ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Schedule:</strong> {{ $course->schedule ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Teacher:</strong> {{ $course->teacher->name ?? 'Unknown' }}</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">৳{{ number_format($course->price, 2) }}</h3>

                    @auth
                    @if($isEnrolled)
                    <div class="alert alert-success text-center">
                        You are enrolled in this course.
                    </div>
                    <!-- Link to course content would go here -->
                    <button class="btn btn-secondary w-100" disabled>Already Enrolled</button>
                    @else
                    <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Enroll Now</button>
                    </form>
                    @endif
                    @else
                    <div class="alert alert-warning">
                        Please <a href="{{ route('login') }}">login</a> to enroll in this course.
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Login to Buy</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
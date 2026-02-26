@extends('layouts.bootstrap')

@section('title', 'My Courses')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-book"></i> My Courses</span>
        <span class="badge bg-light text-success">{{ $courses->count() }} {{ Str::plural('Course', $courses->count()) }}</span>
    </div>
    <div class="card-body">
        @if($courses->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($courses as $course)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    @if($course->image_url)
                    <img src="{{ $course->image_url }}" class="card-img-top" alt="{{ $course->title }}" style="height: 160px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="bi bi-book text-white" style="font-size: 3rem;"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $course->title }}</h5>
                            @php
                            $typeColors = [
                            'online' => 'bg-info',
                            ];
                            $badgeClass = $typeColors[$course->class_type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $course->class_type_label }}</span>
                        </div>
                        <p class="card-text text-muted small">{{ Str::limit($course->description, 80) }}</p>

                        <!-- Course Progress -->
                        @if($course->start_date && $course->end_date)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Course Progress</small>
                                <small class="fw-bold">{{ $course->progress_percentage }}%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                $progress = $course->progress_percentage;
                                $progressClass = 'bg-success';
                                if ($progress < 25) $progressClass='bg-info' ;
                                    elseif ($progress < 50) $progressClass='bg-primary' ;
                                    elseif ($progress < 75) $progressClass='bg-warning' ;
                                    @endphp
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                    style="width: {{ $progress }}%">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Course Stats -->
                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span><i class="bi bi-people"></i> {{ $course->students->count() }} Students</span>
                        <span><i class="bi bi-currency-dollar"></i> {{ number_format($course->price, 2) }}</span>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-3">
                        @if(!$course->is_active)
                        <span class="badge bg-secondary">Inactive</span>
                        @elseif($course->hasEnded())
                        <span class="badge bg-danger">Ended</span>
                        @elseif($course->hasStarted())
                        <span class="badge bg-success">In Progress</span>
                        @else
                        <span class="badge bg-primary">Starting Soon</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-sm btn-outline-success flex-grow-1">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('teacher.courses.students', $course->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="bi bi-people"></i> Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
        <h5 class="mt-3 text-muted">No Courses Assigned</h5>
        <p class="text-muted">You don't have any courses assigned to you yet.</p>
    </div>
    @endif
</div>
</div>

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>
@endsection
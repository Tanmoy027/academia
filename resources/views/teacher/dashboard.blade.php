@extends('layouts.bootstrap')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        Teacher Dashboard
    </div>
    <div class="card-body">
        <h5 class="card-title">Welcome {{ Auth::user()->name }}!</h5>
        <p class="card-text">Manage your classes, students, and grades here.</p>
        <a href="#" class="btn btn-success">View Classes</a>
    </div>
</div>

<!-- Notices & Announcements Section -->
@if($notices->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <span><i class="bi bi-megaphone"></i> Notices & Announcements</span>
        <a href="{{ route('teacher.notices') }}" class="btn btn-sm btn-outline-dark">View All</a>
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
@endsection
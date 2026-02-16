@extends('layouts.bootstrap')

@section('title', 'Notices & Announcements')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Notices & Announcements</h2>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- Category Filter -->
    <div class="mb-4">
        <div class="btn-group" role="group">
            <a href="{{ route('student.notices') }}" class="btn {{ !request('category') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('student.notices', ['category' => 'urgent']) }}" class="btn {{ request('category') == 'urgent' ? 'btn-danger' : 'btn-outline-danger' }}">Urgent</a>
            <a href="{{ route('student.notices', ['category' => 'exam']) }}" class="btn {{ request('category') == 'exam' ? 'btn-warning' : 'btn-outline-warning' }}">Exam</a>
            <a href="{{ route('student.notices', ['category' => 'holiday']) }}" class="btn {{ request('category') == 'holiday' ? 'btn-success' : 'btn-outline-success' }}">Holiday</a>
            <a href="{{ route('student.notices', ['category' => 'class_update']) }}" class="btn {{ request('category') == 'class_update' ? 'btn-info' : 'btn-outline-info' }}">Class Update</a>
            <a href="{{ route('student.notices', ['category' => 'event']) }}" class="btn {{ request('category') == 'event' ? 'btn-primary' : 'btn-outline-primary' }}">Event</a>
        </div>
    </div>

    @if($notices->isEmpty())
    <div class="alert alert-info">
        No notices available at the moment.
    </div>
    @else
    <div class="row">
        @foreach($notices as $notice)
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
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
                            <span class="badge {{ $badgeClass }} mb-2">{{ $notice->category_label }}</span>
                            <h5 class="card-title">{{ $notice->title }}</h5>
                        </div>
                        <small class="text-muted">{{ $notice->created_at->format('M d, Y') }}</small>
                    </div>
                    <p class="card-text">{{ $notice->content }}</p>
                    <small class="text-muted">Posted {{ $notice->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $notices->links() }}
    </div>
    @endif
</div>
@endsection
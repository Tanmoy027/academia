@extends('layouts.bootstrap')

@section('title', 'My Performance - ' . $course->title)

@section('content')
<div class="mb-3 d-flex justify-content-between flex-wrap gap-2">
    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary">
        <i class="bi bi-journal-text"></i> Course Page
    </a>
</div>

<div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #667eea, #764ba2);">
    <div class="card-body text-white p-4 d-flex flex-wrap gap-3 justify-content-between align-items-center">
        <div>
            <p class="text-uppercase small mb-1">Course</p>
            <h3 class="fw-bold mb-1">{{ $course->title }}</h3>
            <p class="mb-0 text-white-50">Instructor: {{ $course->teacher->name ?? 'TBA' }}</p>
        </div>
        <div class="text-end">
            <p class="text-uppercase small mb-1">Overall Score</p>
            <h1 class="display-4 fw-bold mb-0">{{ $performance['score']['overall'] }}%</h1>
            <span class="badge bg-light text-dark">{{ $performance['score']['label'] }}</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="insight-card h-100">
            <div class="icon bg-primary text-white"><i class="bi bi-calendar-check"></i></div>
            <p class="text-uppercase text-muted small mb-1">Attendance</p>
            <h2 class="fw-bold">{{ $performance['attendance']['rate'] !== null ? $performance['attendance']['rate'] . '%' : '—' }}</h2>
            <p class="text-muted mb-0">{{ $performance['attendance']['present'] }} of {{ $performance['attendance']['total_sessions'] }} sessions</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="insight-card h-100">
            <div class="icon bg-success text-white"><i class="bi bi-clipboard-check"></i></div>
            <p class="text-uppercase text-muted small mb-1">Assignments</p>
            <h2 class="fw-bold">{{ $performance['assignments']['completion_rate'] !== null ? $performance['assignments']['completion_rate'] . '%' : '—' }}</h2>
            <p class="text-muted mb-0">{{ $performance['assignments']['submitted'] }} of {{ $performance['assignments']['total'] }} submitted</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="insight-card h-100">
            <div class="icon bg-warning text-dark"><i class="bi bi-bar-chart"></i></div>
            <p class="text-uppercase text-muted small mb-1">Test Average</p>
            <h2 class="fw-bold">{{ $performance['assignments']['average_score'] !== null ? $performance['assignments']['average_score'] . '%' : '—' }}</h2>
            <p class="text-muted mb-0">{{ $performance['assignments']['tests_taken'] }} graded submissions</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 px-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-activity text-primary me-2"></i>Progress Breakdown</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted">Attendance details</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Present</span>
                        <strong>{{ $performance['attendance']['present'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Late</span>
                        <strong>{{ $performance['attendance']['late'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Excused</span>
                        <strong>{{ $performance['attendance']['excused'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Absent</span>
                        <strong>{{ $performance['attendance']['absent'] }}</strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted">Assignments & tests</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Submitted</span>
                        <strong>{{ $performance['assignments']['submitted'] }} / {{ $performance['assignments']['total'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Missing</span>
                        <strong>{{ $performance['assignments']['missing'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Average Score</span>
                        <strong>{{ $performance['assignments']['average_score'] !== null ? $performance['assignments']['average_score'] . '%' : '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tests / Quizzes</span>
                        <strong>{{ $performance['assignments']['tests_taken'] }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Next Steps</h5>
        <ul class="mb-0">
            <li>Focus on attending all upcoming classes to keep your streak strong.</li>
            <li>Review feedback on graded assignments to boost your average.</li>
            <li>Reach out to your teacher if you need help closing any gaps.</li>
        </ul>
    </div>
</div>

<style>
    .insight-card {
        border-radius: 16px;
        border: 1px solid #f1f1f4;
        padding: 1.5rem;
        background: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .insight-card .icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
</style>
@endsection
@extends('layouts.bootstrap')

@section('title', 'Student Performance - ' . $student->name)

@section('content')
<div class="mb-3 d-flex justify-content-between flex-wrap gap-2">
    <a href="{{ route('teacher.courses.performance', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Performance Overview
    </a>
    <a href="mailto:{{ $student->email }}" class="btn btn-outline-primary">
        <i class="bi bi-envelope"></i> Email {{ $student->name }}
    </a>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
    <div class="card-body p-4 d-flex flex-wrap gap-3 align-items-center">
        <div class="avatar bg-primary text-white fw-bold" style="width: 64px; height: 64px; font-size: 1.5rem;">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div class="flex-grow-1">
            <p class="text-uppercase text-muted small mb-1">Student</p>
            <h3 class="fw-bold mb-1">{{ $student->name }}</h3>
            <p class="mb-0 text-muted">{{ $student->email }}</p>
        </div>
        <div class="text-end">
            <small class="text-muted text-uppercase">Overall Score</small>
            @php
            $stateColors = [
            'excellent' => 'success',
            'on-track' => 'primary',
            'warning' => 'warning',
            'risk' => 'danger',
            ];
            $badgeClass = $stateColors[$performance['score']['state']] ?? 'secondary';
            @endphp
            <h1 class="display-5 fw-bold mb-0">{{ $performance['score']['overall'] }}%</h1>
            <span class="badge bg-{{ $badgeClass }}">{{ $performance['score']['label'] }}</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="metric-card h-100">
            <p class="text-uppercase text-muted small mb-1">Attendance</p>
            <h2 class="fw-bold">{{ $performance['attendance']['rate'] !== null ? $performance['attendance']['rate'] . '%' : '—' }}</h2>
            <p class="text-muted mb-2">{{ $performance['attendance']['present'] }} of {{ $performance['attendance']['total_sessions'] }} sessions present</p>
            <div class="d-flex gap-3 small">
                <span class="text-success"><i class="bi bi-check-circle me-1"></i>Present {{ $performance['attendance']['present'] }}</span>
                <span class="text-warning"><i class="bi bi-exclamation-circle me-1"></i>Late {{ $performance['attendance']['late'] }}</span>
                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Absent {{ $performance['attendance']['absent'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card h-100">
            <p class="text-uppercase text-muted small mb-1">Assignments</p>
            <h2 class="fw-bold">{{ $performance['assignments']['completion_rate'] !== null ? $performance['assignments']['completion_rate'] . '%' : '—' }}</h2>
            <p class="text-muted mb-2">{{ $performance['assignments']['submitted'] }} of {{ $performance['assignments']['total'] }} submitted</p>
            <p class="text-muted mb-0">Missing: <strong>{{ $performance['assignments']['missing'] }}</strong></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card h-100">
            <p class="text-uppercase text-muted small mb-1">Test / Quiz Average</p>
            <h2 class="fw-bold">{{ $performance['assignments']['average_score'] !== null ? $performance['assignments']['average_score'] . '%' : '—' }}</h2>
            <p class="text-muted mb-0">{{ $performance['assignments']['tests_taken'] }} graded submissions</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
    <div class="card-header bg-white border-0 px-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up text-primary me-2"></i>Performance Breakdown</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted">Attendance Details</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Regularity</span>
                        <strong>{{ $performance['attendance']['rate'] !== null ? $performance['attendance']['rate'] . '%' : '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Excused</span>
                        <strong>{{ $performance['attendance']['excused'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Late</span>
                        <strong>{{ $performance['attendance']['late'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Absent</span>
                        <strong>{{ $performance['attendance']['absent'] }}</strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted">Assignments & Tests</h6>
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

<div class="alert alert-info border-0" style="border-radius: 16px;">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-lightbulb" style="font-size: 1.75rem;"></i>
        <div>
            <h6 class="fw-bold mb-1">Coaching Tip</h6>
            <p class="mb-0">Use these insights to celebrate wins and identify areas for intervention. Share personalised feedback with the student directly from this page.</p>
        </div>
    </div>
</div>

<style>
    .metric-card {
        background: #fdfdfd;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid #f1f1f4;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
</style>
@endsection
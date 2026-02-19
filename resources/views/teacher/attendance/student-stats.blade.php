@extends('layouts.bootstrap')

@section('title', 'Attendance Stats - ' . $student->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.students', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Students
    </a>
</div>

<!-- Student Info Card -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="avatar-circle-lg me-3">
                {{ substr($student->name, 0, 1) }}
            </div>
            <div>
                <h4 class="mb-0">{{ $student->name }}</h4>
                <p class="text-muted mb-0">{{ $student->email }}</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <h5 class="mb-3">Course: {{ $course->title }}</h5>
        
        <!-- Overall Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <small>Total Classes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $stats['present'] }}</h3>
                        <small>Present</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $stats['absent'] }}</h3>
                        <small>Absent</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $stats['late'] }}</h3>
                        <small>Late</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance by Type -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h6 class="mb-3">Attendance by Class Type</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-event fs-1 text-primary"></i>
                                <h4 class="mt-2">{{ $stats['by_type']['regular'] }}</h4>
                                <small class="text-muted">Regular Classes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-plus fs-1 text-info"></i>
                                <h4 class="mt-2">{{ $stats['by_type']['makeup'] }}</h4>
                                <small class="text-muted">Make-up Classes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-pencil-square fs-1 text-warning"></i>
                                <h4 class="mt-2">{{ $stats['by_type']['quiz'] }}</h4>
                                <small class="text-muted">Quizzes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Percentage -->
        @if($stats['total'] > 0)
        <div class="card bg-light mb-4">
            <div class="card-body">
                <h6 class="mb-3">Attendance Rate</h6>
                @php
                    $attendanceRate = round(($stats['present'] / $stats['total']) * 100, 1);
                    $progressColor = $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger');
                @endphp
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-{{ $progressColor }}" role="progressbar" 
                         style="width: {{ $attendanceRate }}%;" 
                         aria-valuenow="{{ $attendanceRate }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $attendanceRate }}%
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    Based on {{ $stats['present'] }} present out of {{ $stats['total'] }} total classes
                </small>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detailed Attendance History -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Attendance History</h5>
    </div>
    <div class="card-body">
        @if($attendances->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No attendance records yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-info">{{ $attendance->type_label }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($attendance->status) {
                                        'present' => 'bg-success',
                                        'absent' => 'bg-danger',
                                        'late' => 'bg-warning text-dark',
                                        'excused' => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $attendance->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($attendance->notes)
                                    <small>{{ $attendance->notes }}</small>
                                @else
                                    <small class="text-muted fst-italic">No notes</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
.avatar-circle-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 24px;
}
</style>
@endsection

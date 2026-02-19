@extends('layouts.bootstrap')

@section('title', 'Attendance Details - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.attendance', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Attendance
    </a>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ $course->title }}</h4>
            <p class="mb-0 text-muted">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }} | 
                <strong>Type:</strong> {{ ucfirst($type) }} Class
            </p>
        </div>
        <a href="{{ route('teacher.attendance.edit', ['id' => $course->id, 'date' => $date, 'type' => $type]) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>
    <div class="card-body">
        @if($attendances->isEmpty())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> No attendance records found for this date and type.
            </div>
        @else
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $attendances->where('status', 'present')->count() }}</h3>
                            <small>Present</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $attendances->where('status', 'absent')->count() }}</h3>
                            <small>Absent</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $attendances->where('status', 'late')->count() }}</h3>
                            <small>Late</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $attendances->where('status', 'excused')->count() }}</h3>
                            <small>Excused</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed List -->
            <h5 class="mb-3">Student Details</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $index => $attendance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($attendance->student->name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('teacher.attendance.student-stats', ['id' => $course->id, 'studentId' => $attendance->student->id]) }}" 
                                       class="text-decoration-none">
                                        {{ $attendance->student->name }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $attendance->student->email }}</small>
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
                                    <small class="text-muted">{{ $attendance->notes }}</small>
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
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endsection

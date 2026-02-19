@extends('layouts.bootstrap')

@section('title', 'Attendance - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>
</div>

<!-- Course Info -->
<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title">{{ $course->title }}</h3>
        <p class="card-text text-muted">Attendance Management</p>
    </div>
</div>

<!-- Mark New Attendance Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Attendance Records</h4>
    <a href="{{ route('teacher.attendance.mark', $course->id) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Mark Attendance
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Attendance Records -->
@if($attendances->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> No attendance records yet. Click "Mark Attendance" to get started.
</div>
@else
<div class="row">
    @foreach($attendances as $key => $records)
        @php
            list($date, $type) = explode('_', $key);
            $firstRecord = $records->first();
            $totalStudents = $records->count();
            $presentCount = $records->where('status', 'present')->count();
            $absentCount = $records->where('status', 'absent')->count();
            $lateCount = $records->where('status', 'late')->count();
            $excusedCount = $records->where('status', 'excused')->count();
        @endphp
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h5>
                        <span class="badge bg-info">{{ ucfirst($type) }} Class</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('teacher.attendance.show', ['id' => $course->id, 'date' => $date, 'type' => $type]) }}">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('teacher.attendance.edit', ['id' => $course->id, 'date' => $date, 'type' => $type]) }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ $presentCount }}</div>
                                    <small class="text-muted">Present</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill text-danger fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ $absentCount }}</div>
                                    <small class="text-muted">Absent</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock-fill text-warning fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ $lateCount }}</div>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill text-info fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ $excusedCount }}</div>
                                    <small class="text-muted">Excused</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Total Students: {{ $totalStudents }}</small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection

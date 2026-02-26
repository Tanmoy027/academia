@extends('layouts.bootstrap')

@section('title', 'Performance Insights - ' . $course->title)

@section('content')
<div class="mb-3 d-flex justify-content-between flex-wrap gap-2">
    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>
    <a href="{{ route('teacher.courses.students', $course->id) }}" class="btn btn-primary">
        <i class="bi bi-people"></i> Manage Students
    </a>
</div>

<div class="card mb-4 shadow-sm border-0" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="text-uppercase text-muted small mb-1">Course</p>
                <h3 class="fw-bold mb-1">{{ $course->title }}</h3>
                <p class="text-muted mb-0">{{ $course->students->count() }} {{ Str::plural('Student', $course->students->count()) }}</p>
            </div>
            <div class="d-flex gap-3 flex-wrap">
                <div class="stat-pill">
                    <span class="label">Avg. Score</span>
                    <strong>{{ $summary['average_score'] !== null ? $summary['average_score'] . '%' : 'No data' }}</strong>
                </div>
                <div class="stat-pill">
                    <span class="label">Avg. Attendance</span>
                    <strong>{{ $summary['average_attendance'] !== null ? $summary['average_attendance'] . '%' : 'No data' }}</strong>
                </div>
                <div class="stat-pill">
                    <span class="label">Avg. Completion</span>
                    <strong>{{ $summary['average_completion'] !== null ? $summary['average_completion'] . '%' : 'No data' }}</strong>
                </div>
                <div class="stat-pill">
                    <span class="label text-danger">At Risk</span>
                    <strong>{{ $summary['at_risk'] }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($summary['leaders']) && $summary['leaders']->count() > 0)
<div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center px-4">
        <h5 class="mb-0 fw-bold"><i class="bi bi-trophy text-warning me-2"></i>Top Performers</h5>
        <small class="text-muted">Based on overall performance score</small>
    </div>
    <div class="card-body pt-0">
        <div class="list-group list-group-flush">
            @foreach($summary['leaders'] as $leader)
            <div class="list-group-item d-flex align-items-center gap-3">
                <div class="avatar bg-primary text-white fw-bold">
                    {{ strtoupper(substr($leader['student']->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $leader['student']->name }}</h6>
                    <small class="text-muted">Assignments: {{ $leader['assignments']['completion_rate'] ?? '—' }}% | Attendance: {{ $leader['attendance']['rate'] ?? '—' }}%</small>
                </div>
                <span class="badge rounded-pill bg-success">
                    {{ $leader['score']['overall'] }}%
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-data text-primary me-2"></i>Student Performance</h5>
            <span class="badge bg-light text-primary">
                {{ $performances->count() }} {{ Str::plural('Record', $performances->count()) }}
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Student</th>
                    <th>Attendance</th>
                    <th>Assignments</th>
                    <th>Avg Test</th>
                    <th>Overall</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($performances as $performance)
                @php
                $student = $performance['student'];
                $stateColors = [
                'excellent' => 'success',
                'on-track' => 'primary',
                'warning' => 'warning',
                'risk' => 'danger',
                ];
                $badgeClass = $stateColors[$performance['score']['state']] ?? 'secondary';
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar bg-primary text-white fw-bold">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $student->name }}</h6>
                                <small class="text-muted">{{ $student->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $performance['attendance']['rate'] !== null ? $performance['attendance']['rate'] . '%' : '—' }}</div>
                        <small class="text-muted">{{ $performance['attendance']['present'] }} / {{ $performance['attendance']['total_sessions'] }} sessions</small>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $performance['assignments']['completion_rate'] !== null ? $performance['assignments']['completion_rate'] . '%' : '—' }}</div>
                        <small class="text-muted">{{ $performance['assignments']['submitted'] }} / {{ $performance['assignments']['total'] }} assignments</small>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $performance['assignments']['average_score'] !== null ? $performance['assignments']['average_score'] . '%' : '—' }}</div>
                        <small class="text-muted">{{ $performance['assignments']['tests_taken'] }} graded</small>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $performance['score']['overall'] }}%</div>
                        <span class="badge bg-{{ $badgeClass }}">
                            {{ $performance['score']['label'] }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('teacher.students.performance', ['courseId' => $course->id, 'studentId' => $student->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-graph-up"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">No performance data available yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .stat-pill {
        background: #f5f7fb;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        min-width: 140px;
    }

    .stat-pill .label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.05em;
    }

    .avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
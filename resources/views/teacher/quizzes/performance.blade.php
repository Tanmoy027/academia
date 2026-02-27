@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3">Class Performance - {{ $quiz->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('teacher.course.quizzes', $course->id) }}">Quizzes</a></li>
                <li class="breadcrumb-item active">Performance</li>
            </ol>
        </nav>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people-fill text-primary display-4"></i>
                    <h6 class="mt-2 text-muted">Total Attempts</h6>
                    <h2>{{ $statistics['total_attempts'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-graph-up text-success display-4"></i>
                    <h6 class="mt-2 text-muted">Average Score</h6>
                    <h2>{{ $statistics['average_score'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-trophy-fill text-warning display-4"></i>
                    <h6 class="mt-2 text-muted">Highest Score</h6>
                    <h2>{{ $statistics['highest_score'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-percent text-info display-4"></i>
                    <h6 class="mt-2 text-muted">Pass Rate</h6>
                    <h2>{{ $statistics['pass_rate'] }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Distribution Chart -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Score Distribution</h5>
        </div>
        <div class="card-body">
            @php
                $ranges = ['0-40' => 0, '41-50' => 0, '51-60' => 0, '61-70' => 0, '71-80' => 0, '81-90' => 0, '91-100' => 0];
                foreach($attempts as $attempt) {
                    $percentage = $attempt->percentage;
                    if ($percentage <= 40) $ranges['0-40']++;
                    elseif ($percentage <= 50) $ranges['41-50']++;
                    elseif ($percentage <= 60) $ranges['51-60']++;
                    elseif ($percentage <= 70) $ranges['61-70']++;
                    elseif ($percentage <= 80) $ranges['71-80']++;
                    elseif ($percentage <= 90) $ranges['81-90']++;
                    else $ranges['91-100']++;
                }
                $maxCount = max($ranges) ?: 1;
            @endphp

            @foreach($ranges as $range => $count)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ $range }}%</span>
                        <span class="badge bg-primary">{{ $count }} students</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($count / $maxCount) * 100 }}%">
                            {{ $count }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Performers -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Student Rankings</h5>
        </div>
        <div class="card-body">
            @if($attempts->isEmpty())
                <p class="text-muted text-center py-5">No attempts yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student</th>
                                <th>Marks</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $index => $attempt)
                                <tr>
                                    <td>
                                        @if($index === 0)
                                            <i class="bi bi-trophy-fill text-warning fs-4"></i>
                                        @elseif($index === 1)
                                            <i class="bi bi-trophy-fill text-secondary fs-5"></i>
                                        @elseif($index === 2)
                                            <i class="bi bi-trophy-fill text-danger fs-6"></i>
                                        @else
                                            <span class="text-muted">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($attempt->student->profile_picture_url)
                                                <img src="{{ $attempt->student->profile_picture_url }}" alt="{{ $attempt->student->name }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    {{ substr($attempt->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            {{ $attempt->student->name }}
                                        </div>
                                    </td>
                                    <td>{{ $attempt->marks_obtained }} / {{ $quiz->total_marks }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px; min-width: 100px;">
                                            <div class="progress-bar bg-{{ $attempt->isPassed() ? 'success' : 'danger' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $attempt->percentage }}%">
                                                {{ number_format($attempt->percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attempt->getStatusColor() }}">
                                            {{ $attempt->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $attempt->submitted_at?->format('M d, H:i') ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

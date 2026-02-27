@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3">Quiz Results - {{ $quiz->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('teacher.course.quizzes', $course->id) }}">Quizzes</a></li>
                <li class="breadcrumb-item active">Results</li>
            </ol>
        </nav>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Attempts</h6>
                    <h2 class="mb-0">{{ $statistics['total_attempts'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Average Score</h6>
                    <h2 class="mb-0">{{ $statistics['average_score'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Highest Score</h6>
                    <h2 class="mb-0">{{ $statistics['highest_score'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Pass Rate</h6>
                    <h2 class="mb-0">{{ $statistics['pass_rate'] }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Attempts List -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Student Attempts</h5>
            <a href="{{ route('teacher.quiz.performance', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-graph-up me-2"></i>View Performance
            </a>
        </div>
        <div class="card-body">
            @if($attempts->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-data display-1 text-muted"></i>
                    <p class="text-muted mt-3">No student attempts yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Started At</th>
                                <th>Submitted At</th>
                                <th>Time Spent</th>
                                <th>Marks Obtained</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
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
                                    <td>{{ $attempt->started_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                                    <td>{{ $attempt->submitted_at?->format('M d, Y H:i') ?? 'Not submitted' }}</td>
                                    <td>{{ number_format($attempt->getTimeSpentMinutes(), 1) }} mins</td>
                                    <td>{{ $attempt->marks_obtained }} / {{ $quiz->total_marks }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $attempt->isPassed() ? 'success' : 'danger' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $attempt->percentage }}%"
                                                     aria-valuenow="{{ $attempt->percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($attempt->percentage, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attempt->getStatusColor() }}">
                                            {{ $attempt->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.quiz.attempt.view', ['courseId' => $course->id, 'quizId' => $quiz->id, 'attemptId' => $attempt->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </td>
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

@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Available Quizzes</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Active Quizzes -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-play-circle me-2"></i>Active Quizzes</h5>
        </div>
        <div class="card-body">
            @if($activeQuizzes->isEmpty())
                <p class="text-muted mb-0">No active quizzes at the moment.</p>
            @else
                <div class="row">
                    @foreach($activeQuizzes as $quiz)
                        <div class="col-md-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $quiz->title }}</h5>
                                    <p class="card-text text-muted">{{ $quiz->course->title }}</p>
                                    <ul class="list-unstyled mb-3">
                                        <li><i class="bi bi-clock me-2"></i><strong>Duration:</strong> {{ $quiz->duration_minutes }} minutes</li>
                                        <li><i class="bi bi-trophy me-2"></i><strong>Total Marks:</strong> {{ $quiz->total_marks }}</li>
                                        <li><i class="bi bi-question-circle me-2"></i><strong>Questions:</strong> {{ $quiz->questions->count() }}</li>
                                        <li><i class="bi bi-calendar-x me-2"></i><strong>Ends:</strong> {{ $quiz->end_time->format('M d, Y - H:i A') }}</li>
                                    </ul>
                                    @if($quiz->studentHasAttempted(auth()->id()))
                                        @php $attempt = $quiz->getStudentAttempt(auth()->id()); @endphp
                                        @if($attempt->is_submitted)
                                            <a href="{{ route('student.quiz.result', $quiz->id) }}" class="btn btn-info w-100">
                                                <i class="bi bi-eye me-2"></i>View Result
                                            </a>
                                        @else
                                            <a href="{{ route('student.quiz.start', $quiz->id) }}" class="btn btn-warning w-100">
                                                <i class="bi bi-arrow-clockwise me-2"></i>Resume Quiz
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('student.quiz.start', $quiz->id) }}" class="btn btn-success w-100">
                                            <i class="bi bi-play-circle me-2"></i>Start Quiz
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Upcoming Quizzes -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Upcoming Quizzes</h5>
        </div>
        <div class="card-body">
            @if($upcomingQuizzes->isEmpty())
                <p class="text-muted mb-0">No upcoming quizzes.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Course</th>
                                <th>Duration</th>
                                <th>Marks</th>
                                <th>Starts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingQuizzes as $quiz)
                                <tr>
                                    <td>{{ $quiz->title }}</td>
                                    <td>{{ $quiz->course->title }}</td>
                                    <td>{{ $quiz->duration_minutes }} mins</td>
                                    <td>{{ $quiz->total_marks }}</td>
                                    <td>{{ $quiz->start_time->format('M d, Y - H:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Completed Quizzes -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Completed Quizzes</h5>
        </div>
        <div class="card-body">
            @if($completedQuizzes->isEmpty())
                <p class="text-muted mb-0">No completed quizzes.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Course</th>
                                <th>Marks</th>
                                <th>Ended</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedQuizzes as $quiz)
                                <tr>
                                    <td>{{ $quiz->title }}</td>
                                    <td>{{ $quiz->course->title }}</td>
                                    <td>{{ $quiz->total_marks }}</td>
                                    <td>{{ $quiz->end_time->format('M d, Y') }}</td>
                                    <td>
                                        @if($quiz->studentHasAttempted(auth()->id()))
                                            @php $attempt = $quiz->getStudentAttempt(auth()->id()); @endphp
                                            @if($attempt->is_submitted)
                                                <span class="badge bg-success">Submitted</span>
                                            @else
                                                <span class="badge bg-danger">Missed</span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">Not Attempted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($quiz->studentHasAttempted(auth()->id()) && $quiz->getStudentAttempt(auth()->id())->is_submitted)
                                            <a href="{{ route('student.quiz.result', $quiz->id) }}" class="btn btn-sm btn-info">
                                                View Result
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
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
</div>
@endsection

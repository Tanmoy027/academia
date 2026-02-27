@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Quizzes - {{ $course->title }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.courses') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course->id) }}">{{ Str::limit($course->title, 30) }}</a></li>
                    <li class="breadcrumb-item active">Quizzes</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('teacher.quiz.create', $course->id) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Quiz
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quiz Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Quizzes</h6>
                    <h2 class="mb-0">{{ $quizzes->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Published</h6>
                    <h2 class="mb-0">{{ $quizzes->where('is_published', true)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Drafts</h6>
                    <h2 class="mb-0">{{ $quizzes->where('is_published', false)->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Quizzes List -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">All Quizzes</h5>
        </div>
        <div class="card-body">
            @if($quizzes->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x display-1 text-muted"></i>
                    <p class="text-muted mt-3">No quizzes created yet. Create your first quiz to get started!</p>
                    <a href="{{ route('teacher.quiz.create', $course->id) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Your First Quiz
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Duration</th>
                                <th>Total Marks</th>
                                <th>Questions</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Status</th>
                                <th>Attempts</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <a href="{{ route('teacher.quiz.show', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="text-decoration-none fw-semibold">
                                            {{ $quiz->title }}
                                        </a>
                                        @if(!$quiz->is_published)
                                            <span class="badge bg-secondary ms-2">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $quiz->duration_minutes }} mins</td>
                                    <td>{{ $quiz->total_marks }}</td>
                                    <td>{{ $quiz->questions->count() }}</td>
                                    <td>{{ $quiz->start_time->format('M d, Y H:i') }}</td>
                                    <td>{{ $quiz->end_time->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($quiz->hasEnded())
                                            <span class="badge bg-secondary">Ended</span>
                                        @elseif($quiz->isActive())
                                            <span class="badge bg-success">Active</span>
                                        @elseif($quiz->hasStarted())
                                            <span class="badge bg-warning">Started</span>
                                        @else
                                            <span class="badge bg-info">Upcoming</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.quiz.results', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="text-decoration-none">
                                            {{ $quiz->studentAttempts->count() }} attempts
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teacher.quiz.show', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.quiz.edit', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('teacher.quiz.results', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-outline-info" title="Results">
                                                <i class="bi bi-bar-chart"></i>
                                            </a>
                                            <form action="{{ route('teacher.quiz.delete', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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

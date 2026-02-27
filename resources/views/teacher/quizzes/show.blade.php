@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">{{ $quiz->title }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.course.quizzes', $course->id) }}">Quizzes</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($quiz->title, 30) }}</li>
                </ol>
            </nav>
        </div>
        <div>
            @if(!$quiz->is_published)
                <form action="{{ route('teacher.quiz.publish', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" {{ $questions->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-2"></i>Publish Quiz
                    </button>
                </form>
            @else
                <form action="{{ route('teacher.quiz.unpublish', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-x-circle me-2"></i>Unpublish
                    </button>
                </form>
            @endif
            <a href="{{ route('teacher.quiz.edit', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
        </div>
    </div>

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

    <!-- Quiz Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quiz Details</h5>
                    @if($quiz->description)
                        <p class="card-text">{{ $quiz->description }}</p>
                    @endif
                    <div class="row mt-3">
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted">Duration</small>
                            <p class="mb-0 fw-semibold">{{ $quiz->duration_minutes }} mins</p>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted">Total Marks</small>
                            <p class="mb-0 fw-semibold">{{ $quiz->total_marks }}</p>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted">Questions</small>
                            <p class="mb-0 fw-semibold">{{ $questions->count() }}</p>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                @if(!$quiz->is_published)
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($quiz->hasEnded())
                                    <span class="badge bg-secondary">Ended</span>
                                @elseif($quiz->isActive())
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-info">Upcoming</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Start Time</small>
                            <p class="mb-0">{{ $quiz->start_time->format('M d, Y - H:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">End Time</small>
                            <p class="mb-0">{{ $quiz->end_time->format('M d, Y - H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Quick Actions</h6>
                    <a href="{{ route('teacher.quiz.questions.create', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="bi bi-plus-circle me-2"></i>Add Question
                    </a>
                    <a href="{{ route('teacher.quiz.results', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-info btn-sm w-100 mb-2">
                        <i class="bi bi-bar-chart me-2"></i>View Results
                    </a>
                    <a href="{{ route('teacher.quiz.performance', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-secondary btn-sm w-100">
                        <i class="bi bi-graph-up me-2"></i>Class Performance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Questions ({{ $questions->count() }})</h5>
            <a href="{{ route('teacher.quiz.questions.create', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Question
            </a>
        </div>
        <div class="card-body">
            @if($questions->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-question-circle display-1 text-muted"></i>
                    <p class="text-muted mt-3">No questions added yet. Add questions to activate this quiz.</p>
                    <a href="{{ route('teacher.quiz.questions.create', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Your First Question
                    </a>
                </div>
            @else
                @foreach($questions as $index => $question)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        <span class="badge bg-secondary me-2">Q{{ $index + 1 }}</span>
                                        {{ $question->question_text }}
                                        <span class="badge bg-primary ms-2">{{ $question->marks }} marks</span>
                                        @if($question->question_type === 'mcq')
                                            <span class="badge bg-info ms-2">MCQ</span>
                                        @else
                                            <span class="badge bg-warning ms-2">Short Answer</span>
                                        @endif
                                    </h6>
                                    
                                    @if($question->question_type === 'mcq' && $question->options->isNotEmpty())
                                        <div class="ms-4 mt-2">
                                            @foreach($question->options as $optIndex => $option)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="radio" disabled {{ $option->is_correct ? 'checked' : '' }}>
                                                    <label class="form-check-label {{ $option->is_correct ? 'fw-bold text-success' : '' }}">
                                                        {{ chr(65 + $optIndex) }}. {{ $option->option_text }}
                                                        @if($option->is_correct)
                                                            <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm ms-3">
                                    <a href="{{ route('teacher.quiz.questions.edit', ['courseId' => $course->id, 'quizId' => $quiz->id, 'questionId' => $question->id]) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('teacher.quiz.questions.delete', ['courseId' => $course->id, 'quizId' => $quiz->id, 'questionId' => $question->id]) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

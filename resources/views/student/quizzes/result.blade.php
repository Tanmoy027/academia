@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="h3">Quiz Result</h1>
        <p class="text-muted">{{ $quiz->title }}</p>
    </div>

    <!-- Score Card -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card border-{{ $attempt->isPassed() ? 'success' : 'danger' }}">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        @if($attempt->isPassed())
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        @endif
                    </div>
                    <h2 class="mb-3">{{ $attempt->isPassed() ? 'Congratulations!' : 'Keep Trying!' }}</h2>
                    <h1 class="display-4 fw-bold mb-3">
                        {{ $attempt->marks_obtained }} / {{ $quiz->total_marks }}
                    </h1>
                    <h3 class="mb-4">{{ number_format($attempt->percentage, 2) }}%</h3>
                    <span class="badge bg-{{ $attempt->getStatusColor() }} fs-5 px-4 py-2">
                        {{ $attempt->getStatusLabel() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-clock text-primary fs-3"></i>
                    <h6 class="mt-2 text-muted">Time Taken</h6>
                    <p class="mb-0 fw-bold">{{ number_format($attempt->getTimeSpentMinutes(), 1) }} mins</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-question-circle text-info fs-3"></i>
                    <h6 class="mt-2 text-muted">Total Questions</h6>
                    <p class="mb-0 fw-bold">{{ $quiz->questions->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success fs-3"></i>
                    <h6 class="mt-2 text-muted">Correct</h6>
                    <p class="mb-0 fw-bold">{{ $responses->where('isCorrect', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-x-circle text-danger fs-3"></i>
                    <h6 class="mt-2 text-muted">Incorrect</h6>
                    <p class="mb-0 fw-bold">{{ $responses->filter(function($r) { return $r->question->isMCQ() && !$r->isCorrect(); })->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Review -->
    @if($quiz->show_correct_answers)
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Answer Review</h5>
            </div>
            <div class="card-body">
                @foreach($responses as $index => $response)
                    <div class="card mb-3">
                        <div class="card-header {{ $response->isCorrect() ? 'bg-success' : ($response->question->isShortAnswer() ? 'bg-warning' : 'bg-danger') }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Question {{ $index + 1 }}</span>
                                <span>
                                    @if($response->question->isMCQ())
                                        @if($response->isCorrect())
                                            <i class="bi bi-check-circle-fill me-2"></i>Correct
                                        @else
                                            <i class="bi bi-x-circle-fill me-2"></i>Incorrect
                                        @endif
                                    @else
                                        <i class="bi bi-pencil me-2"></i>Short Answer ({{ $response->marks_awarded ?? 'Pending' }} / {{ $response->question->marks }} marks)
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="fw-semibold">{{ $response->question->question_text }}</p>

                            @if($response->question->isMCQ())
                                <div class="ms-3">
                                    @foreach($response->question->options as $optIndex => $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" disabled 
                                                   {{ $response->selected_option_id == $option->id ? 'checked' : '' }}>
                                            <label class="form-check-label {{ $option->is_correct ? 'fw-bold text-success' : '' }} {{ $response->selected_option_id == $option->id && !$option->is_correct ? 'text-danger' : '' }}">
                                                {{ chr(65 + $optIndex) }}. {{ $option->option_text }}
                                                @if($option->is_correct)
                                                    <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                                @endif
                                                @if($response->selected_option_id == $option->id && !$option->is_correct)
                                                    <i class="bi bi-x-circle-fill text-danger ms-2"></i>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-light p-3 rounded">
                                    <strong>Your Answer:</strong>
                                    <p class="mb-0 mt-2">{{ $response->answer_text }}</p>
                                </div>
                                @if($response->teacher_feedback)
                                    <div class="alert alert-info mt-3">
                                        <strong>Teacher Feedback:</strong>
                                        <p class="mb-0">{{ $response->teacher_feedback }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            The correct answers are not available for this quiz.
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('student.quizzes.available') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Back to Quizzes
        </a>
    </div>
</div>
@endsection

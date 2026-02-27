@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h4">Student Quiz Attempt - {{ $attempt->student->name }}</h1>
        <p class="text-muted">{{ $quiz->title }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Score Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted">Marks Obtained</h6>
                    <h3>{{ $attempt->marks_obtained }} / {{ $quiz->total_marks }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Percentage</h6>
                    <h3>{{ number_format($attempt->percentage, 2) }}%</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Time Spent</h6>
                    <h3>{{ number_format($attempt->getTimeSpentMinutes(), 1) }} mins</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Status</h6>
                    <h3><span class="badge bg-{{ $attempt->getStatusColor() }}">{{ $attempt->getStatusLabel() }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Responses -->
    @foreach($responses as $index => $response)
        <div class="card mb-3">
            <div class="card-header {{ $response->isCorrect() ? 'bg-success' : ($response->question->isShortAnswer() ? 'bg-light' : 'bg-danger') }} {{ $response->isCorrect() || $response->question->isMCQ() ? 'text-white' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Question {{ $index + 1 }}</span>
                    <span>
                        @if($response->question->isMCQ())
                            @if($response->isCorrect())
                                <i class="bi bi-check-circle-fill me-2"></i>Correct ({{ $response->question->marks }} marks)
                            @else
                                <i class="bi bi-x-circle-fill me-2"></i>Incorrect (0 marks)
                            @endif
                        @else
                            <i class="bi bi-pencil me-2"></i>Short Answer ({{ $response->question->marks }} marks)
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
                    <!-- Short Answer Grading -->
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>Student's Answer:</strong>
                        <p class="mb-0 mt-2">{{ $response->answer_text ?: '(No answer provided)' }}</p>
                    </div>

                    <form action="{{ route('teacher.quiz.response.grade', ['courseId' => $course->id, 'quizId' => $quiz->id, 'attemptId' => $attempt->id, 'responseId' => $response->id]) }}" method="POST" class="border-top pt-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="marks_{{ $response->id }}" class="form-label">Marks Awarded <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="marks_{{ $response->id }}" name="marks_awarded" 
                                       value="{{ $response->marks_awarded ?? 0 }}" 
                                       min="0" max="{{ $response->question->marks }}" 
                                       step="0.5" required>
                                <small class="text-muted">Max: {{ $response->question->marks }}</small>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="feedback_{{ $response->id }}" class="form-label">Teacher Feedback</label>
                                <textarea class="form-control" id="feedback_{{ $response->id }}" name="teacher_feedback" rows="2" placeholder="Optional feedback for the student">{{ $response->teacher_feedback }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Save Grade
                        </button>
                        @if($response->marks_awarded !== null)
                            <span class="badge bg-success ms-2">Graded</span>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    @endforeach

    <div class="text-center mt-4">
        <a href="{{ route('teacher.quiz.results', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Results
        </a>
    </div>
</div>
@endsection

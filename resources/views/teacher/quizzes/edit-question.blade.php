@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Question</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Error!</strong> Please fix the issues:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('teacher.quiz.questions.update', ['courseId' => $course->id, 'quizId' => $quiz->id, 'questionId' => $question->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Question Type</label>
                            <input type="text" class="form-control" value="{{ $question->question_type === 'mcq' ? 'Multiple Choice (MCQ)' : 'Short Answer' }}" disabled>
                            <small class="text-muted">Question type cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="marks" name="marks" value="{{ old('marks', $question->marks) }}" required min="1" max="100">
                        </div>

                        @if($question->question_type === 'mcq')
                            <h6 class="mb-3">Answer Options</h6>
                            @foreach($question->options as $index => $option)
                                <div class="mb-3">
                                    <label class="form-label">Option {{ chr(65 + $index) }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="options[]" value="{{ $option->option_text }}" required>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }} required>
                                            <label class="ms-2">Correct</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('teacher.quiz.show', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add Question - {{ $quiz->title }}</h5>
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

                    <form action="{{ route('teacher.quiz.questions.store', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST" id="questionForm">
                        @csrf

                        <div class="mb-3">
                            <label for="question_type" class="form-label">Question Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="question_type" name="question_type" required>
                                <option value="">Select Type</option>
                                <option value="mcq" {{ old('question_type') == 'mcq' ? 'selected' : '' }}>Multiple Choice (MCQ)</option>
                                <option value="short_answer" {{ old('question_type') == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required>{{ old('question_text') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="marks" name="marks" value="{{ old('marks', 10) }}" required min="1" max="100">
                        </div>

                        <!-- MCQ Options (shown only for MCQ type) -->
                        <div id="mcq_options" style="display: none;">
                            <h6 class="mb-3">Answer Options</h6>
                            <div id="options_container">
                                <div class="mb-3 option-group" data-option-index="0">
                                    <label class="form-label">Option A</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control mcq-option-input" name="options[]" placeholder="Enter option text" disabled>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0 mcq-correct-radio" type="radio" name="correct_option" value="0" disabled>
                                            <label class="ms-2">Correct</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 option-group" data-option-index="1">
                                    <label class="form-label">Option B</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control mcq-option-input" name="options[]" placeholder="Enter option text" disabled>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0 mcq-correct-radio" type="radio" name="correct_option" value="1" disabled>
                                            <label class="ms-2">Correct</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add_option">
                                <i class="bi bi-plus-circle me-2"></i>Add Option
                            </button>
                            <p class="text-muted mt-2 small">
                                <i class="bi bi-info-circle me-1"></i>Select the radio button to mark the correct answer
                            </p>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('teacher.quiz.show', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Add Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let optionCount = 2;

    const questionTypeSelect = document.getElementById('question_type');
    const mcqOptionsDiv = document.getElementById('mcq_options');

    function toggleMCQOptions() {
        const optionInputs = document.querySelectorAll('.mcq-option-input');
        const correctRadios = document.querySelectorAll('.mcq-correct-radio');
        
        if (questionTypeSelect.value === 'mcq') {
            mcqOptionsDiv.style.display = 'block';
            // Enable all option inputs and radio buttons
            optionInputs.forEach(input => {
                input.disabled = false;
                input.required = true;
            });
            correctRadios.forEach(radio => {
                radio.disabled = false;
                radio.required = true;
            });
        } else {
            mcqOptionsDiv.style.display = 'none';
            // Disable all option inputs and radio buttons
            optionInputs.forEach(input => {
                input.disabled = true;
                input.required = false;
            });
            correctRadios.forEach(radio => {
                radio.disabled = true;
                radio.required = false;
            });
        }
    }

    questionTypeSelect.addEventListener('change', toggleMCQOptions);

    document.getElementById('add_option').addEventListener('click', function() {
        const container = document.getElementById('options_container');
        const letter = String.fromCharCode(65 + optionCount);
        
        const optionHtml = `
            <div class="mb-3 option-group" data-option-index="${optionCount}">
                <label class="form-label">Option ${letter}</label>
                <div class="input-group">
                    <input type="text" class="form-control mcq-option-input" name="options[]" placeholder="Enter option text" required>
                    <div class="input-group-text">
                        <input class="form-check-input mt-0 mcq-correct-radio" type="radio" name="correct_option" value="${optionCount}" required>
                        <label class="ms-2">Correct</label>
                    </div>
                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.option-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', optionHtml);
        optionCount++;
    });

    // Trigger on page load if old value exists
    if (questionTypeSelect.value) {
        toggleMCQOptions();
    }
})();
</script>
@endsection

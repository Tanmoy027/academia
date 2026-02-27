@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <!-- Timer Header -->
    <div class="card bg-dark text-white mb-4 position-sticky top-0" style="z-index: 1000;">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">{{ $quiz->title }}</h5>
                    <small>{{ $quiz->course->title }}</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <h3 class="mb-0" id="timer">
                        <i class="bi bi-clock me-2"></i>
                        <span id="time-display">{{ $quiz->duration_minutes }}:00</span>
                    </h3>
                    <small>Time Remaining</small>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Important:</strong> Your responses are auto-saved. The quiz will auto-submit when time expires.
    </div>

    <form id="quizForm" action="{{ route('student.quiz.submit', ['quizId' => $quiz->id, 'attemptId' => $existingAttempt->id]) }}" method="POST">
        @csrf

        @foreach($questions as $index => $question)
            <div class="card mb-4 question-card" data-question-id="{{ $question->id }}">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        Question {{ $index + 1 }} of {{ $questions->count() }}
                        <span class="badge bg-light text-dark float-end">{{ $question->marks }} marks</span>
                    </h6>
                </div>
                <div class="card-body">
                    <p class="fw-semibold">{{ $question->question_text }}</p>

                    @if($question->question_type === 'mcq')
                        @php
                            $savedResponse = $existingAttempt->getResponseToQuestion($question->id);
                        @endphp
                        <div class="mcq-options">
                            @foreach($question->options as $optIndex => $option)
                                <div class="form-check mb-2">
                                    <input class="form-check-input mcq-option" 
                                           type="radio" 
                                           name="question_{{ $question->id }}" 
                                           id="option_{{ $option->id }}" 
                                           value="{{ $option->id }}"
                                           data-question-id="{{ $question->id }}"
                                           {{ $savedResponse && $savedResponse->selected_option_id == $option->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="option_{{ $option->id }}">
                                        {{ chr(65 + $optIndex) }}. {{ $option->option_text }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->question_type === 'short_answer')
                        @php
                            $savedResponse = $existingAttempt->getResponseToQuestion($question->id);
                        @endphp
                        <textarea class="form-control short-answer" 
                                  name="question_{{ $question->id }}" 
                                  data-question-id="{{ $question->id }}"
                                  rows="4" 
                                  placeholder="Type your answer here...">{{ $savedResponse?->answer_text ?? '' }}</textarea>
                    @endif

                    <div class="mt-2">
                        <small class="text-success save-status" id="status_{{ $question->id }}" style="display: none;">
                            <i class="bi bi-check-circle me-1"></i>Saved
                        </small>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card bg-light">
            <div class="card-body text-center">
                <p class="mb-3">Make sure you've answered all questions before submitting.</p>
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Are you sure you want to submit? You cannot change your answers after submission.')">
                    <i class="bi bi-check-circle me-2"></i>Submit Quiz
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const attemptId = {{ $existingAttempt->id }};
const quizId = {{ $quiz->id }};
const csrfToken = '{{ csrf_token() }}';
let remainingSeconds = {{ $existingAttempt->getRemainingSeconds() }};
let timerInterval;

// Timer function
function updateTimer() {
    if (remainingSeconds <= 0) {
        clearInterval(timerInterval);
        alert('Time is up! Quiz will be submitted automatically.');
        document.getElementById('quizForm').submit();
        return;
    }

    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('time-display').textContent = display;

    // Change color when time is running out
    const timerElement = document.getElementById('timer');
    if (remainingSeconds < 60) {
        timerElement.classList.add('text-danger');
    } else if (remainingSeconds < 300) {
        timerElement.classList.add('text-warning');
    }

    remainingSeconds--;
}

// Start timer
timerInterval = setInterval(updateTimer, 1000);
updateTimer();

// Auto-save responses
function saveResponse(questionId, responseData) {
    fetch(`/student/quizzes/${quizId}/attempts/${attemptId}/save-response`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            question_id: questionId,
            ...responseData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusElement = document.getElementById(`status_${questionId}`);
            statusElement.style.display = 'inline';
            setTimeout(() => {
                statusElement.style.display = 'none';
            }, 2000);
        }
    })
    .catch(error => console.error('Error saving response:', error));
}

// MCQ option change handler
document.querySelectorAll('.mcq-option').forEach(radio => {
    radio.addEventListener('change', function() {
        const questionId = this.getAttribute('data-question-id');
        const selectedOptionId = this.value;
        saveResponse(questionId, { selected_option_id: selectedOptionId });
    });
});

// Short answer change handler with debounce
let debounceTimers = {};
document.querySelectorAll('.short-answer').forEach(textarea => {
    textarea.addEventListener('input', function() {
        const questionId = this.getAttribute('data-question-id');
        const answerText = this.value;

        // Clear previous timer
        if (debounceTimers[questionId]) {
            clearTimeout(debounceTimers[questionId]);
        }

        // Set new timer to save after 1 second of inactivity
        debounceTimers[questionId] = setTimeout(() => {
            saveResponse(questionId, { answer_text: answerText });
        }, 1000);
    });
});

// Prevent accidental page close
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
    return '';
});

// Remove warning when form is submitted
document.getElementById('quizForm').addEventListener('submit', function() {
    window.removeEventListener('beforeunload', arguments.callee);
});
</script>
@endpush
@endsection

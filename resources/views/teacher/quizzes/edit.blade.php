@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Quiz - {{ $quiz->title }}</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Error!</strong> Please fix the following issues:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('teacher.quiz.update', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="500">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $quiz->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="total_marks" name="total_marks" value="{{ old('total_marks', $quiz->total_marks) }}" required min="1" max="1000">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" required min="1" max="480">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', $quiz->start_time->format('Y-m-d\TH:i')) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ends At (auto)</label>
                                <input type="text" class="form-control" id="calculated_end_time" value="{{ $quiz->end_time->format('M d, Y h:i A') }}" readonly>
                                <small class="form-text text-muted">This updates automatically based on the start time and duration.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>Quiz Settings</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="show_correct_answers" name="show_correct_answers" value="1" {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_correct_answers">
                                    Show correct answers after submission
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_questions">
                                    Shuffle questions for each student
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.quiz.show', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startInput = document.getElementById('start_time');
        const durationInput = document.getElementById('duration_minutes');
        const calculatedEndInput = document.getElementById('calculated_end_time');

        const updateCalculatedEnd = () => {
            if (!startInput || !durationInput || !calculatedEndInput) {
                return;
            }

            if (!startInput.value || !durationInput.value) {
                calculatedEndInput.value = 'Set start time and duration';
                return;
            }

            const startDate = new Date(startInput.value);
            const duration = parseInt(durationInput.value, 10);

            if (Number.isNaN(startDate.getTime()) || Number.isNaN(duration)) {
                calculatedEndInput.value = 'Waiting for valid input';
                return;
            }

            const endDate = new Date(startDate.getTime() + duration * 60000);
            calculatedEndInput.value = endDate.toLocaleString();
        };

        updateCalculatedEnd();

        if (startInput) {
            startInput.addEventListener('change', updateCalculatedEnd);
        }
        if (durationInput) {
            durationInput.addEventListener('input', updateCalculatedEnd);
        }
    });
</script>
@endpush
@endsection
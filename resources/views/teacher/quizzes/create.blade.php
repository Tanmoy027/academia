@extends('layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Create New Quiz - {{ $course->title }}</h5>
                </div>
                <div class="card-body">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.courses') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course->id) }}">{{ Str::limit($course->title, 30) }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.course.quizzes', $course->id) }}">Quizzes</a></li>
                            <li class="breadcrumb-item active">Create Quiz</li>
                        </ol>
                    </nav>

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

                    <form action="{{ route('teacher.quiz.store', $course->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required maxlength="500">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Provide instructions or details about the quiz</small>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}" required min="1" max="1000">
                                @error('total_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 30) }}" required min="1" max="480">
                                <small class="form-text text-muted">Maximum 480 minutes (8 hours)</small>
                                @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ends At (auto)</label>
                                <input type="text" class="form-control" id="calculated_end_time" value="" readonly>
                                <small class="form-text text-muted">Calculated from the start time and duration.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>Quiz Settings</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="show_correct_answers" name="show_correct_answers" value="1" {{ old('show_correct_answers') ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_correct_answers">
                                    Show correct answers after submission
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_questions">
                                    Shuffle questions for each student
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> End time will be generated automatically from the start time + duration. After creating the quiz, add questions before publishing it.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.course.quizzes', $course->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Quiz
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

        const now = new Date();
        const minValue = now.toISOString().slice(0, 16);
        if (startInput) {
            startInput.min = minValue;
        }

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
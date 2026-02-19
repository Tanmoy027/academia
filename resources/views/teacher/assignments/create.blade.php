@extends('layouts.bootstrap')

@section('title', 'Create Assignment - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.assignments', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Assignments
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h6 class="mb-2"><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h6>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Create New Assignment</h4>
        <p class="text-muted mb-0">{{ $course->title }}</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('teacher.assignments.store', $course->id) }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Assignment Title -->
            <div class="mb-3">
                <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title') }}" 
                       placeholder="Enter assignment title" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description / Instructions</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="6"
                          placeholder="Enter detailed assignment instructions...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Provide clear instructions for students</small>
            </div>

            <!-- Due Date and Points -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="due_date" class="form-label">Due Date & Time</label>
                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                           id="due_date" name="due_date" value="{{ old('due_date') }}">
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Leave empty for no due date</small>
                </div>
                
                <div class="col-md-6">
                    <label for="max_points" class="form-label">Maximum Points <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_points') is-invalid @enderror" 
                           id="max_points" name="max_points" value="{{ old('max_points', 100) }}" 
                           min="1" max="1000" required>
                    @error('max_points')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Attachment -->
            <div class="mb-3">
                <label for="attachment" class="form-label">Attachment (Optional)</label>
                <input type="file" class="form-control @error('attachment') is-invalid @enderror" 
                       id="attachment" name="attachment" accept=".pdf,.doc,.docx,.txt,.zip">
                @error('attachment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Allowed: PDF, DOC, DOCX, TXT, ZIP. Max size: 10MB</small>
            </div>

            <!-- Publish Status -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" 
                           {{ old('is_published', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published">
                        Publish immediately (students can see this assignment)
                    </label>
                </div>
                <small class="text-muted">Uncheck to save as draft</small>
            </div>

            <hr class="my-4">

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('teacher.courses.assignments', $course->id) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Assignment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.bootstrap')

@section('title', 'Materials - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>
</div>

<!-- Course Header -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-book"></i> {{ $course->title }}</span>
            @php
            $typeColors = [
            'online' => 'bg-info',
            ];
            $badgeClass = $typeColors[$course->class_type] ?? 'bg-secondary';
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $course->class_type_label }}</span>
        </div>
    </div>
</div>

<!-- Upload Section -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-cloud-upload"></i> Upload Course Materials
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Coming Soon!</strong> The file upload feature is currently under development.
            You'll be able to upload PDFs, documents, videos, and other course materials here.
        </div>

        <!-- Upload Form Design (Non-functional) -->
        <div class="border-2 border-dashed rounded p-5 text-center bg-light" style="border-style: dashed !important;">
            <i class="bi bi-cloud-arrow-up text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Drag and drop files here</h5>
            <p class="text-muted mb-3">or</p>
            <button class="btn btn-primary" disabled>
                <i class="bi bi-folder2-open"></i> Browse Files
            </button>
            <p class="text-muted small mt-3 mb-0">
                Supported formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, MP4, ZIP<br>
                Maximum file size: 50MB
            </p>
        </div>
    </div>
</div>

<!-- Materials List (Design Preview) -->
<div class="card">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-folder"></i> Course Materials</span>
        <span class="badge bg-light text-secondary">0 Files</span>
    </div>
    <div class="card-body">
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No Materials Yet</h5>
            <p class="text-muted">Upload your first course material using the form above.</p>
        </div>

        <!-- Sample Material Items (Hidden - for design reference) -->
        <div class="d-none">
            <div class="list-group">
                <!-- Sample PDF -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-file-pdf text-danger" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Course Syllabus.pdf</h6>
                            <small class="text-muted">2.4 MB • Uploaded Jan 15, 2025</small>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" title="Download">
                            <i class="bi bi-download"></i>
                        </button>
                        <button class="btn btn-outline-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- Sample Video -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-file-play text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Introduction Video.mp4</h6>
                            <small class="text-muted">45.8 MB • Uploaded Jan 16, 2025</small>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" title="Download">
                            <i class="bi bi-download"></i>
                        </button>
                        <button class="btn btn-outline-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- Sample Document -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-file-word text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Assignment 1.docx</h6>
                            <small class="text-muted">156 KB • Uploaded Jan 17, 2025</small>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" title="Download">
                            <i class="bi bi-download"></i>
                        </button>
                        <button class="btn btn-outline-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-md-3 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="bi bi-file-pdf text-danger" style="font-size: 1.5rem;"></i>
                <h5 class="mt-2 mb-0">0</h5>
                <small class="text-muted">Documents</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="bi bi-file-play text-primary" style="font-size: 1.5rem;"></i>
                <h5 class="mt-2 mb-0">0</h5>
                <small class="text-muted">Videos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark text-secondary" style="font-size: 1.5rem;"></i>
                <h5 class="mt-2 mb-0">0</h5>
                <small class="text-muted">Other Files</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="bi bi-hdd text-warning" style="font-size: 1.5rem;"></i>
                <h5 class="mt-2 mb-0">0 MB</h5>
                <small class="text-muted">Total Size</small>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>
@endsection
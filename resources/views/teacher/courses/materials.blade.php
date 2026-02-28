@extends('layouts.bootstrap')

@section('title', 'Materials - ' . $course->title)

@section('content')
<div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
    <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>
    <span class="badge bg-light text-dark">
        <i class="bi bi-folder me-1"></i>{{ $stats['count'] }} files available to students
    </span>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <strong>Upload failed:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Course Header -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-book me-2"></i>{{ $course->title }}
            <div class="small text-white-50">{{ $course->schedule ?? 'Flexible schedule' }}</div>
        </div>
        @php
        $typeColors = [
        'online' => 'bg-info',
        ];
        $badgeClass = $typeColors[$course->class_type] ?? 'bg-secondary';
        @endphp
        <span class="badge {{ $badgeClass }} text-uppercase">{{ $course->class_type_label }}</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-cloud-upload me-2"></i>Upload Course Materials
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.courses.materials.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="material-title" class="form-label">Title <span class="text-muted small">(optional)</span></label>
                        <input type="text" id="material-title" name="title" class="form-control" placeholder="E.g. Week 1 Slides" value="{{ old('title') }}" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="material-description" class="form-label">Description <span class="text-muted small">(optional)</span></label>
                        <textarea id="material-description" name="description" class="form-control" rows="3" placeholder="Add context for students (links, instructions, etc.)" maxlength="1000">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="material-file" class="form-label">Select File <span class="text-danger">*</span></label>
                        <input type="file" id="material-file" name="file" class="form-control" required>
                        <small class="text-muted d-block mt-2">
                            Supported: PDF, DOC(X), PPT(X), XLS(X), CSV, TXT, images, ZIP/RAR, MP4/MOV/WMV/AVI/MKV/WEBM, MP3/WAV. Max 50MB.
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload & Share
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light fw-semibold">
                <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Storage Snapshot
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="bi bi-file-earmark-text text-primary d-block mb-1"></i>
                            <h5 class="mb-0">{{ $stats['documents'] }}</h5>
                            <small class="text-muted">Documents</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="bi bi-file-play text-success d-block mb-1"></i>
                            <h5 class="mb-0">{{ $stats['videos'] }}</h5>
                            <small class="text-muted">Videos</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="bi bi-file-earmark text-secondary d-block mb-1"></i>
                            <h5 class="mb-0">{{ $stats['others'] }}</h5>
                            <small class="text-muted">Other Files</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="bi bi-hdd text-warning d-block mb-1"></i>
                            <h5 class="mb-0">{{ $stats['size_human'] }}</h5>
                            <small class="text-muted">Total Size</small>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light border mt-3 mb-0">
                    <i class="bi bi-info-circle me-2 text-primary"></i>
                    Files are stored in your Supabase <strong>courseprofile</strong> bucket and shared instantly with enrolled students.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Materials List -->
<div class="card mt-4 shadow-sm">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-folder me-2"></i>Course Materials</span>
        <span class="badge bg-light text-secondary">
            {{ $stats['count'] }} {{ Str::plural('file', $stats['count']) }}
        </span>
    </div>
    <div class="card-body">
        @if($materials->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No materials yet</h5>
            <p class="text-muted mb-0">Upload your first file using the form above.</p>
        </div>
        @else
        <div class="list-group list-group-flush">
            @foreach($materials as $material)
            @php
            $extension = $material->file_extension;
            $iconMap = [
            'pdf' => 'bi-file-earmark-pdf text-danger',
            'doc' => 'bi-file-earmark-word text-primary',
            'docx' => 'bi-file-earmark-word text-primary',
            'ppt' => 'bi-file-earmark-slides text-warning',
            'pptx' => 'bi-file-earmark-slides text-warning',
            'xls' => 'bi-file-earmark-spreadsheet text-success',
            'xlsx' => 'bi-file-earmark-spreadsheet text-success',
            'csv' => 'bi-file-earmark-spreadsheet text-success',
            'mp4' => 'bi-file-earmark-play text-success',
            'mov' => 'bi-file-earmark-play text-success',
            'wmv' => 'bi-file-earmark-play text-success',
            'avi' => 'bi-file-earmark-play text-success',
            'mkv' => 'bi-file-earmark-play text-success',
            'webm' => 'bi-file-earmark-play text-success',
            'mp3' => 'bi-music-note-beamed text-info',
            'wav' => 'bi-music-note-beamed text-info',
            'zip' => 'bi-file-earmark-zip text-warning',
            'rar' => 'bi-file-earmark-zip text-warning',
            ];
            $iconClass = $iconMap[$extension] ?? (str_contains(strtolower($material->file_type ?? ''), 'video') ? 'bi-file-earmark-play text-success' : 'bi-file-earmark text-secondary');
            @endphp
            <div class="list-group-item py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="fs-3"><i class="bi {{ $iconClass }}"></i></div>
                        <div>
                            <h6 class="mb-1">{{ $material->title }}</h6>
                            <div class="text-muted small mb-1">
                                {{ $material->size_for_humans }} • Uploaded {{ $material->created_at->format('M d, Y g:i A') }}
                            </div>
                            @if($material->description)
                            <p class="mb-0 text-muted">{{ $material->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ $material->file_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-copy data-url="{{ $material->file_url }}">Copy</button>
                        <form action="{{ route('teacher.courses.materials.destroy', [$course->id, $material->id]) }}" method="POST" onsubmit="return confirm('Delete {{ $material->title }}? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('[data-copy]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            navigator.clipboard.writeText(url).then(() => {
                this.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(() => this.innerHTML = 'Copy', 1500);
            });
        });
    });
</script>
@endsection
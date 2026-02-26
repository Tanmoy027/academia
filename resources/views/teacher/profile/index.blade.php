@extends('layouts.bootstrap')

@section('title', 'My Profile')

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Profile Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <!-- Profile Picture -->
                <div class="position-relative d-inline-block">
                    @if($teacher->profile_picture_url)
                    <img src="{{ $teacher->profile_picture_url }}" alt="{{ $teacher->name }}"
                        class="rounded-circle img-thumbnail"
                        style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                        style="width: 200px; height: 200px; font-size: 80px; font-weight: bold;">
                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                    </div>
                    @endif

                    <!-- Upload/Delete Picture Buttons -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPictureModal">
                            <i class="bi bi-camera"></i> Change Photo
                        </button>
                        @if($teacher->profile_picture_url)
                        <form method="POST" action="{{ route('teacher.profile.picture.delete') }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete your profile picture?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="mb-1">{{ $teacher->name }}</h2>
                        <p class="text-muted mb-2">
                            <i class="bi bi-envelope"></i> {{ $teacher->email }}
                        </p>
                        @if($teacher->phone)
                        <p class="text-muted mb-2">
                            <i class="bi bi-telephone"></i> {{ $teacher->phone }}
                        </p>
                        @endif
                        @if($teacher->specialization)
                        <span class="badge bg-info">{{ $teacher->specialization }}</span>
                        @endif
                    </div>
                    <a href="{{ route('teacher.profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>

                @if($teacher->bio)
                <div class="mb-3">
                    <h6 class="text-muted">About</h6>
                    <p>{{ $teacher->bio }}</p>
                </div>
                @endif

                <!-- Statistics -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $coursesCount }}</h3>
                                <small>Courses Teaching</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $studentsCount }}</h3>
                                <small>Total Students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $teacher->experience_years }}</h3>
                                <small>Years Experience</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Information -->
<div class="row">
    <!-- Professional Information -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-briefcase"></i> Professional Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Qualification</h6>
                    @if($teacher->qualification)
                    <p class="mb-0">{{ $teacher->qualification }}</p>
                    @else
                    <p class="text-muted fst-italic mb-0">Not provided</p>
                    @endif
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Specialization</h6>
                    @if($teacher->specialization)
                    <p class="mb-0">{{ $teacher->specialization }}</p>
                    @else
                    <p class="text-muted fst-italic mb-0">Not provided</p>
                    @endif
                </div>

                <div>
                    <h6 class="text-muted mb-1">Teaching Experience</h6>
                    <p class="mb-0">{{ $teacher->experience_years }} years</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Address</h6>
                    @if($teacher->address)
                    <p class="mb-0">{{ $teacher->address }}</p>
                    @else
                    <p class="text-muted fst-italic mb-0">Not provided</p>
                    @endif
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">City</h6>
                    @if($teacher->city)
                    <p class="mb-0">{{ $teacher->city }}</p>
                    @else
                    <p class="text-muted fst-italic mb-0">Not provided</p>
                    @endif
                </div>

                <div>
                    <h6 class="text-muted mb-1">Country</h6>
                    @if($teacher->country)
                    <p class="mb-0">{{ $teacher->country }}</p>
                    @else
                    <p class="text-muted fst-italic mb-0">Not provided</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Social Links -->
@if($teacher->social_links && count($teacher->social_links) > 0)
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-share"></i> Social Links</h5>
    </div>
    <div class="card-body">
        <div class="d-flex gap-3 flex-wrap">
            @if(isset($teacher->social_links['linkedin']))
            <a href="{{ $teacher->social_links['linkedin'] }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-linkedin"></i> LinkedIn
            </a>
            @endif
            @if(isset($teacher->social_links['twitter']))
            <a href="{{ $teacher->social_links['twitter'] }}" target="_blank" class="btn btn-outline-info">
                <i class="bi bi-twitter"></i> Twitter
            </a>
            @endif
            @if(isset($teacher->social_links['facebook']))
            <a href="{{ $teacher->social_links['facebook'] }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-facebook"></i> Facebook
            </a>
            @endif
            @if(isset($teacher->social_links['website']))
            <a href="{{ $teacher->social_links['website'] }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-globe"></i> Website
            </a>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Upload Picture Modal -->
<div class="modal fade" id="uploadPictureModal" tabindex="-1" aria-labelledby="uploadPictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.profile.picture.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPictureModalLabel">Upload Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Choose Image</label>
                        <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                            id="profile_picture" name="profile_picture" accept="image/*" required>
                        @error('profile_picture')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum file size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="text-center d-none">
                        <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 300px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Image preview
    document.getElementById('profile_picture')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
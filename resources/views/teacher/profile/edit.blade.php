@extends('layouts.bootstrap')

@section('title', 'Edit Profile')

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.profile') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Profile
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Edit Profile</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('teacher.profile.update') }}">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <h5 class="mb-3 text-primary"><i class="bi bi-person"></i> Personal Information</h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name', $teacher->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email', $teacher->email) }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                        id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}"
                        placeholder="+1 234 567 8900">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="experience_years" class="form-label">Years of Experience</label>
                    <input type="number" class="form-control @error('experience_years') is-invalid @enderror"
                        id="experience_years" name="experience_years"
                        value="{{ old('experience_years', $teacher->experience_years) }}"
                        min="0" max="99">
                    @error('experience_years')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label">Bio / About</label>
                <textarea class="form-control @error('bio') is-invalid @enderror"
                    id="bio" name="bio" rows="4"
                    placeholder="Tell us about yourself...">{{ old('bio', $teacher->bio) }}</textarea>
                @error('bio')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Maximum 2000 characters</small>
            </div>

            <hr class="my-4">

            <!-- Professional Information -->
            <h5 class="mb-3 text-primary"><i class="bi bi-briefcase"></i> Professional Information</h5>

            <div class="mb-3">
                <label for="qualification" class="form-label">Qualification / Education</label>
                <textarea class="form-control @error('qualification') is-invalid @enderror"
                    id="qualification" name="qualification" rows="3"
                    placeholder="E.g., PhD in Computer Science, Stanford University">{{ old('qualification', $teacher->qualification) }}</textarea>
                @error('qualification')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="specialization" class="form-label">Specialization / Subject Area</label>
                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                    id="specialization" name="specialization"
                    value="{{ old('specialization', $teacher->specialization) }}"
                    placeholder="E.g., Mathematics, Physics, Computer Science">
                @error('specialization')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr class="my-4">

            <!-- Contact Information -->
            <h5 class="mb-3 text-primary"><i class="bi bi-geo-alt"></i> Contact Information</h5>

            <div class="mb-3">
                <label for="address" class="form-label">Street Address</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror"
                    id="address" name="address" value="{{ old('address', $teacher->address) }}"
                    placeholder="123 Main Street">
                @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                        id="city" name="city" value="{{ old('city', $teacher->city) }}"
                        placeholder="New York">
                    @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                        id="country" name="country" value="{{ old('country', $teacher->country) }}"
                        placeholder="United States">
                    @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <!-- Social Links -->
            <h5 class="mb-3 text-primary"><i class="bi bi-share"></i> Social Links</h5>

            <div class="mb-3">
                <label for="linkedin" class="form-label">
                    <i class="bi bi-linkedin"></i> LinkedIn Profile
                </label>
                <input type="url" class="form-control @error('linkedin') is-invalid @enderror"
                    id="linkedin" name="linkedin"
                    value="{{ old('linkedin', $teacher->social_links['linkedin'] ?? '') }}"
                    placeholder="https://linkedin.com/in/yourprofile">
                @error('linkedin')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="twitter" class="form-label">
                    <i class="bi bi-twitter"></i> Twitter Profile
                </label>
                <input type="url" class="form-control @error('twitter') is-invalid @enderror"
                    id="twitter" name="twitter"
                    value="{{ old('twitter', $teacher->social_links['twitter'] ?? '') }}"
                    placeholder="https://twitter.com/yourhandle">
                @error('twitter')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="facebook" class="form-label">
                    <i class="bi bi-facebook"></i> Facebook Profile
                </label>
                <input type="url" class="form-control @error('facebook') is-invalid @enderror"
                    id="facebook" name="facebook"
                    value="{{ old('facebook', $teacher->social_links['facebook'] ?? '') }}"
                    placeholder="https://facebook.com/yourprofile">
                @error('facebook')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="website" class="form-label">
                    <i class="bi bi-globe"></i> Personal Website
                </label>
                <input type="url" class="form-control @error('website') is-invalid @enderror"
                    id="website" name="website"
                    value="{{ old('website', $teacher->social_links['website'] ?? '') }}"
                    placeholder="https://yourwebsite.com">
                @error('website')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr class="my-4">

            <!-- Account Security -->
            <h5 class="mb-3 text-primary"><i class="bi bi-shield-lock"></i> Account Security</h5>

            <div class="alert alert-warning small">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Leave the fields below blank if you do not want to change your password.
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                        id="current_password" name="current_password" autocomplete="current-password">
                    @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" autocomplete="new-password">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control"
                        id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
            </div>

            <hr class="my-4">

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('teacher.profile') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-label {
        font-weight: 500;
    }
</style>
@endsection
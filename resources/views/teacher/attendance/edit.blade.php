@extends('layouts.bootstrap')

@section('title', 'Edit Attendance - ' . $course->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('teacher.courses.attendance', $course->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Attendance
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="mb-1">Edit Attendance</h4>
        <p class="text-muted mb-0">
            {{ $course->title }} | {{ \Carbon\Carbon::parse($date)->format('F d, Y') }} | {{ ucfirst($type) }} Class
        </p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('teacher.attendance.store', $course->id) }}">
            @csrf
            
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="type" value="{{ $type }}">

            <!-- Date and Type Info (Read-only) -->
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong> {{ ucfirst($type) }} Class
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Students ({{ $students->count() }})</h5>
            
            @if($students->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> No students enrolled in this course yet.
                </div>
            @else
                <!-- Bulk Actions -->
                <div class="mb-3 p-3 bg-light rounded">
                    <small class="text-muted d-block mb-2">Quick Mark All:</small>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-success" onclick="markAll('present')">
                            <i class="bi bi-check-circle"></i> All Present
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="markAll('absent')">
                            <i class="bi bi-x-circle"></i> All Absent
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="markAll('late')">
                            <i class="bi bi-clock"></i> All Late
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th style="width: 200px;">Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            @php
                                $existingRecord = $existingAttendance->get($student->id);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        {{ $student->name }}
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $student->email }}</small>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm attendance-status @error('attendance.' . $student->id) is-invalid @enderror" 
                                            name="attendance[{{ $student->id }}]" required>
                                        <option value="">Select...</option>
                                        @foreach(\App\Models\Attendance::getStatuses() as $value => $label)
                                            <option value="{{ $value }}" 
                                                {{ ($existingRecord && $existingRecord->status == $value) || old('attendance.' . $student->id) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('attendance.' . $student->id)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="notes[{{ $student->id }}]" 
                                           placeholder="Optional notes"
                                           value="{{ $existingRecord ? $existingRecord->notes : old('notes.' . $student->id) }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('teacher.courses.attendance', $course->id) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Update Attendance
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
function markAll(status) {
    const selects = document.querySelectorAll('.attendance-status');
    selects.forEach(select => {
        select.value = status;
    });
}
</script>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeacherController extends Controller
{
    /**
     * Display a listing of teacher's courses.
     */
    public function courses()
    {
        $teacher = Auth::user();
        $courses = Course::where('teacher_id', $teacher->id)
            ->with('students')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.courses.index', compact('courses'));
    }

    /**
     * Display the specified course with students and materials section.
     */
    public function showCourse($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        return view('teacher.courses.show', compact('course'));
    }

    /**
     * Display the students enrolled in a course.
     */
    public function courseStudents($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        $students = $course->students;

        return view('teacher.courses.students', compact('course', 'students'));
    }

    /**
     * Display the course materials management page.
     */
    public function courseMaterials($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        return view('teacher.courses.materials', compact('course'));
    }

    /**
     * Display attendance records for a course
     */
    public function courseAttendance($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        // Get all attendance records for this course, grouped by date
        $attendances = Attendance::where('course_id', $id)
            ->with('student')
            ->orderBy('date', 'desc')
            ->orderBy('type')
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d') . '_' . $item->type;
            });

        return view('teacher.attendance.index', compact('course', 'attendances'));
    }

    /**
     * Show form to mark attendance for a specific date and type
     */
    public function markAttendance($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        $students = $course->students;
        $types = Attendance::getTypes();
        $statuses = Attendance::getStatuses();

        return view('teacher.attendance.mark', compact('course', 'students', 'types', 'statuses'));
    }

    /**
     * Store attendance records
     */
    public function storeAttendance(Request $request, $id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:regular,makeup,quiz',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:500',
        ]);

        foreach ($validated['attendance'] as $userId => $status) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $userId,
                    'course_id' => $course->id,
                    'date' => $validated['date'],
                    'type' => $validated['type'],
                ],
                [
                    'status' => $status,
                    'notes' => $validated['notes'][$userId] ?? null,
                ]
            );
        }

        return redirect()
            ->route('teacher.courses.attendance', $course->id)
            ->with('success', 'Attendance marked successfully!');
    }

    /**
     * Show attendance for a specific date and type
     */
    public function showAttendance($id, Request $request)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $date = $request->input('date');
        $type = $request->input('type');

        $attendances = Attendance::where('course_id', $id)
            ->where('date', $date)
            ->where('type', $type)
            ->with('student')
            ->get();

        return view('teacher.attendance.show', compact('course', 'attendances', 'date', 'type'));
    }

    /**
     * Edit attendance for a specific date and type
     */
    public function editAttendance($id, Request $request)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        $date = $request->input('date');
        $type = $request->input('type');

        $students = $course->students;
        $types = Attendance::getTypes();
        $statuses = Attendance::getStatuses();

        // Get existing attendance records
        $existingAttendance = Attendance::where('course_id', $id)
            ->where('date', $date)
            ->where('type', $type)
            ->get()
            ->keyBy('user_id');

        return view('teacher.attendance.edit', compact('course', 'students', 'types', 'statuses', 'date', 'type', 'existingAttendance'));
    }

    /**
     * Get attendance statistics for a student in a course
     */
    public function studentAttendanceStats($courseId, $studentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $student = User::findOrFail($studentId);

        // Check if student is enrolled
        $enrollment = Enrollment::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->firstOrFail();

        $attendances = Attendance::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'by_type' => [
                'regular' => $attendances->where('type', 'regular')->count(),
                'makeup' => $attendances->where('type', 'makeup')->count(),
                'quiz' => $attendances->where('type', 'quiz')->count(),
            ]
        ];

        return view('teacher.attendance.student-stats', compact('course', 'student', 'attendances', 'stats'));
    }

    /**
     * Display the teacher's profile
     */
    public function profile()
    {
        $teacher = Auth::user();
        $coursesCount = $teacher->teachingCourses()->count();
        $studentsCount = Enrollment::whereIn('course_id', $teacher->teachingCourses()->pluck('id'))->distinct('user_id')->count();
        
        return view('teacher.profile.index', compact('teacher', 'coursesCount', 'studentsCount'));
    }

    /**
     * Show the form for editing the teacher's profile
     */
    public function editProfile()
    {
        $teacher = Auth::user();
        return view('teacher.profile.edit', compact('teacher'));
    }

    /**
     * Update the teacher's profile
     */
    public function updateProfile(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'qualification' => 'nullable|string|max:1000',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:99',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        // Handle social links
        $socialLinks = [];
        if ($request->filled('linkedin')) {
            $socialLinks['linkedin'] = $request->linkedin;
        }
        if ($request->filled('twitter')) {
            $socialLinks['twitter'] = $request->twitter;
        }
        if ($request->filled('facebook')) {
            $socialLinks['facebook'] = $request->facebook;
        }
        if ($request->filled('website')) {
            $socialLinks['website'] = $request->website;
        }

        $teacher->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'social_links' => !empty($socialLinks) ? $socialLinks : null,
        ]);

        return redirect()
            ->route('teacher.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = 'profile_' . $teacher->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in public/storage/profiles directory
            $path = $file->storeAs('profiles', $filename, 'public');
            
            // Delete old profile picture if exists
            if ($teacher->profile_picture) {
                $oldPath = str_replace('/storage/', '', $teacher->profile_picture);
                \Storage::disk('public')->delete($oldPath);
            }
            
            $teacher->update([
                'profile_picture' => '/storage/' . $path,
            ]);

            return redirect()
                ->route('teacher.profile')
                ->with('success', 'Profile picture updated successfully!');
        }

        return redirect()
            ->route('teacher.profile')
            ->with('error', 'Failed to upload profile picture.');
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture()
    {
        $teacher = Auth::user();

        if ($teacher->profile_picture) {
            $path = str_replace('/storage/', '', $teacher->profile_picture);
            \Storage::disk('public')->delete($path);
            
            $teacher->update([
                'profile_picture' => null,
            ]);

            return redirect()
                ->route('teacher.profile')
                ->with('success', 'Profile picture deleted successfully!');
        }

        return redirect()
            ->route('teacher.profile')
            ->with('error', 'No profile picture to delete.');
    }
}

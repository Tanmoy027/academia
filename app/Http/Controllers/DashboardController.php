<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Notice;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard');
    }

    public function teacher()
    {
        $teacher = Auth::user();

        // Get courses assigned to this teacher
        $courses = Course::where('teacher_id', $teacher->id)
            ->with('students')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get notices for teachers (target_role = 'all' or 'teacher')
        $notices = Notice::forRole('teacher')
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('notices', 'courses'));
    }

    public function student()
    {
        $user = Auth::user();

        // Get all active courses for browsing
        $courses = Course::where('is_active', true)->get();

        // Get notices for students (target_role = 'all' or 'student')
        $notices = Notice::forRole('student')
            ->latest()
            ->take(5)
            ->get();

        // Get enrolled courses for this student
        $enrolledCourses = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->latest()
            ->get();

        // Calculate stats
        $stats = [
            'total_enrolled' => $enrolledCourses->count(),
            'completed' => $enrolledCourses->where('status', 'completed')->count(),
            'in_progress' => $enrolledCourses->where('status', 'active')->count(),
        ];

        // Get upcoming classes/schedule from enrolled courses
        $upcomingClasses = $enrolledCourses->take(3)->map(function ($enrollment) {
            return [
                'course' => $enrollment->course,
                'schedule' => $enrollment->course->schedule ?? 'Not scheduled',
            ];
        });

        return view('student.dashboard', compact('courses', 'notices', 'enrolledCourses', 'stats', 'upcomingClasses'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Notice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard');
    }

    public function teacher()
    {
        // Get notices for teachers (target_role = 'all' or 'teacher')
        $notices = Notice::forRole('teacher')
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('notices'));
    }

    public function student()
    {
        $courses = Course::where('is_active', true)->get();

        // Get notices for students (target_role = 'all' or 'student')
        $notices = Notice::forRole('student')
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact('courses', 'notices'));
    }
}

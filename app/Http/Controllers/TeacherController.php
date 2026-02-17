<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}

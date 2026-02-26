<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\PerformanceService;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function __construct(private readonly PerformanceService $performanceService) {}

    public function teacherCourse($courseId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        $snapshot = $this->performanceService->getCoursePerformanceSnapshot($course);
        $summary = $this->performanceService->summarizeCourse($course, $snapshot);

        return view('teacher.performance.index', [
            'course' => $course,
            'performances' => $snapshot,
            'summary' => $summary,
        ]);
    }

    public function teacherStudent($courseId, $studentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        /** @var User $student */
        $student = $course->students()->where('users.id', $studentId)->firstOrFail();
        $performance = $this->performanceService->getStudentCoursePerformance($student, $course);

        return view('teacher.performance.student', compact('course', 'student', 'performance'));
    }

    public function studentCourse($courseId)
    {
        $student = Auth::user();

        $enrollment = Enrollment::where('user_id', $student->id)
            ->where('course_id', $courseId)
            ->with(['course', 'course.teacher'])
            ->firstOrFail();

        $course = $enrollment->course;
        $performance = $this->performanceService->getStudentCoursePerformance($student, $course);

        return view('student.performance.show', compact('course', 'student', 'performance'));
    }
}

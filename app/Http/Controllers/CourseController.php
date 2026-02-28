<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudentQuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::where('is_active', true)->get();
        return view('courses.index', compact('courses'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        $isEnrolled = false;
        $canEnroll = $course->canEnroll();

        if (Auth::check()) {
            $isEnrolled = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
        }

        $studentAccess = null;

        if ($isEnrolled && Auth::user()?->role === 'student') {
            $student = Auth::user();
            $enrollment = Enrollment::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            $assignments = $course->assignments()
                ->published()
                ->orderBy('due_date')
                ->take(5)
                ->get();

            $assignmentSubmissions = AssignmentSubmission::where('user_id', $student->id)
                ->whereIn('assignment_id', $assignments->pluck('id'))
                ->get()
                ->keyBy('assignment_id');

            $assignments->transform(function ($assignment) use ($assignmentSubmissions) {
                $assignment->setRelation('student_submission', $assignmentSubmissions->get($assignment->id));
                return $assignment;
            });

            $activeQuizzes = $course->quizzes()
                ->where('is_published', true)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->orderBy('start_time')
                ->take(3)
                ->get();

            $upcomingQuizzes = $course->quizzes()
                ->where('is_published', true)
                ->where('start_time', '>', now())
                ->orderBy('start_time')
                ->take(3)
                ->get();

            $recentQuizAttempts = StudentQuizAttempt::where('student_id', $student->id)
                ->whereHas('quiz', fn($query) => $query->where('course_id', $course->id))
                ->with('quiz')
                ->orderByDesc('submitted_at')
                ->take(3)
                ->get();

            $recentAttendance = Attendance::where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->orderByDesc('date')
                ->take(6)
                ->get();

            $attendanceStats = Attendance::selectRaw('status, COUNT(*) as total')
                ->where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->groupBy('status')
                ->pluck('total', 'status');

            $totalAttendance = $attendanceStats->sum();
            $attendanceRate = $totalAttendance > 0
                ? round((($attendanceStats[Attendance::STATUS_PRESENT] ?? 0) / $totalAttendance) * 100)
                : null;

            $studentAccess = [
                'assignments' => $assignments,
                'quizzes' => [
                    'active' => $activeQuizzes,
                    'upcoming' => $upcomingQuizzes,
                    'completed' => $recentQuizAttempts,
                ],
                'attendance' => [
                    'recent' => $recentAttendance,
                    'totals' => [
                        'present' => (int) ($attendanceStats[Attendance::STATUS_PRESENT] ?? 0),
                        'absent' => (int) ($attendanceStats[Attendance::STATUS_ABSENT] ?? 0),
                        'late' => (int) ($attendanceStats[Attendance::STATUS_LATE] ?? 0),
                        'excused' => (int) ($attendanceStats[Attendance::STATUS_EXCUSED] ?? 0),
                    ],
                    'rate' => $attendanceRate,
                ],
                'live_session' => $course->getLiveSessionSnapshot(),
                'next_class_text' => $course->online_schedule ?? $course->schedule,
                'enrollment' => $enrollment,
            ];
        }

        return view('courses.show', compact('course', 'isEnrolled', 'canEnroll', 'studentAccess'));
    }

    /**
     * Enroll the user in the course.
     */
    public function enroll($id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        // Check if already enrolled
        $exists = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->route('courses.show', $id)->with('info', 'You are already enrolled in this course.');
        }

        // Create enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active', // You might want 'pending' if payment is involved
        ]);

        return redirect()->route('courses.show', $id)->with('success', 'Enrolled successfully!');
    }
}

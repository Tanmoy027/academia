<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class PerformanceService
{
    /**
     * Relative weight each attendance status contributes toward the attendance score.
     */
    private const ATTENDANCE_WEIGHTS = [
        Attendance::STATUS_PRESENT => 1.0,
        Attendance::STATUS_LATE => 0.7,
        Attendance::STATUS_EXCUSED => 0.85,
        Attendance::STATUS_ABSENT => 0.0,
    ];

    public function getStudentCoursePerformance(User $student, Course $course): array
    {
        $assignments = Assignment::where('course_id', $course->id)->get();
        $assignmentIds = $assignments->pluck('id');

        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('user_id', $student->id)
            ->get();

        $attendance = Attendance::where('course_id', $course->id)
            ->where('user_id', $student->id)
            ->get();

        return $this->buildStudentMetrics($student, $course, $assignments, $submissions, $attendance);
    }

    public function getCoursePerformanceSnapshot(Course $course): Collection
    {
        $assignments = Assignment::where('course_id', $course->id)->get();
        $assignmentIds = $assignments->pluck('id');

        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->get()
            ->groupBy('user_id');

        $attendanceRecords = Attendance::where('course_id', $course->id)
            ->get()
            ->groupBy('user_id');

        $students = $course->students()->orderBy('name')->get();

        return $students->map(function (User $student) use ($course, $assignments, $submissions, $attendanceRecords) {
            $studentSubmissions = $submissions->get($student->id, collect());
            $studentAttendance = $attendanceRecords->get($student->id, collect());

            return $this->buildStudentMetrics($student, $course, $assignments, $studentSubmissions, $studentAttendance);
        });
    }

    public function summarizeCourse(Course $course, ?Collection $snapshot = null): array
    {
        $snapshot = $snapshot ?? $this->getCoursePerformanceSnapshot($course);

        if ($snapshot->isEmpty()) {
            return [
                'average_score' => null,
                'average_attendance' => null,
                'average_completion' => null,
                'at_risk' => 0,
                'excellent' => 0,
                'leaders' => collect(),
            ];
        }

        $averageScore = $snapshot->avg(fn($item) => $item['score']['overall'] ?? 0) ?? 0;
        $averageAttendance = $snapshot->avg(fn($item) => $item['attendance']['rate'] ?? 0) ?? 0;
        $averageCompletion = $snapshot->avg(fn($item) => $item['assignments']['completion_rate'] ?? 0) ?? 0;

        return [
            'average_score' => round($averageScore, 1),
            'average_attendance' => round($averageAttendance, 1),
            'average_completion' => round($averageCompletion, 1),
            'at_risk' => $snapshot->where('score.state', 'risk')->count(),
            'excellent' => $snapshot->where('score.state', 'excellent')->count(),
            'leaders' => $snapshot->sortByDesc(fn($item) => $item['score']['overall'])->take(3),
        ];
    }

    /**
     * Build an aggregated metric array for a student within a course.
     */
    private function buildStudentMetrics(
        User $student,
        Course $course,
        Collection $assignments,
        Collection $submissions,
        Collection $attendance
    ): array {
        $totalAssignments = $assignments->count();
        $submittedAssignments = $submissions->unique('assignment_id')->count();
        $assignmentCompletion = $totalAssignments > 0
            ? round(($submittedAssignments / $totalAssignments) * 100, 1)
            : null;

        $gradedSubmissions = $submissions->filter(fn($submission) => $submission->grade !== null);
        $totalPointsEarned = $gradedSubmissions->sum('grade');
        $totalPointsPossible = $gradedSubmissions->sum(function ($submission) use ($assignments) {
            return $assignments->firstWhere('id', $submission->assignment_id)?->max_points ?? 0;
        });
        $averageScore = $totalPointsPossible > 0
            ? round(($totalPointsEarned / $totalPointsPossible) * 100, 1)
            : null;

        $attendanceCounts = [
            Attendance::STATUS_PRESENT => $attendance->where('status', Attendance::STATUS_PRESENT)->count(),
            Attendance::STATUS_ABSENT => $attendance->where('status', Attendance::STATUS_ABSENT)->count(),
            Attendance::STATUS_LATE => $attendance->where('status', Attendance::STATUS_LATE)->count(),
            Attendance::STATUS_EXCUSED => $attendance->where('status', Attendance::STATUS_EXCUSED)->count(),
        ];
        $totalSessions = array_sum($attendanceCounts);
        $attendanceRate = $totalSessions > 0
            ? round(($this->calculateAttendanceScore($attendanceCounts) / $totalSessions) * 100, 1)
            : null;

        $overallScore = $this->calculateOverallScore($attendanceRate, $assignmentCompletion, $averageScore);
        $status = $this->statusFromScore($overallScore);

        return [
            'student' => $student,
            'course' => $course,
            'assignments' => [
                'total' => $totalAssignments,
                'submitted' => $submittedAssignments,
                'completion_rate' => $assignmentCompletion,
                'missing' => max(0, $totalAssignments - $submittedAssignments),
                'average_score' => $averageScore,
                'tests_taken' => $gradedSubmissions->count(),
            ],
            'attendance' => [
                'total_sessions' => $totalSessions,
                'present' => $attendanceCounts[Attendance::STATUS_PRESENT],
                'absent' => $attendanceCounts[Attendance::STATUS_ABSENT],
                'late' => $attendanceCounts[Attendance::STATUS_LATE],
                'excused' => $attendanceCounts[Attendance::STATUS_EXCUSED],
                'rate' => $attendanceRate,
            ],
            'score' => [
                'overall' => $overallScore,
                'label' => $status['label'],
                'state' => $status['state'],
            ],
        ];
    }

    private function calculateAttendanceScore(array $counts): float
    {
        $score = 0.0;

        foreach (self::ATTENDANCE_WEIGHTS as $status => $weight) {
            $score += ($counts[$status] ?? 0) * $weight;
        }

        return $score;
    }

    private function calculateOverallScore(?float $attendanceRate, ?float $completionRate, ?float $averageScore): float
    {
        $components = [
            'attendance' => $attendanceRate ?? 0,
            'assignments' => $completionRate ?? 0,
            'tests' => $averageScore ?? 0,
        ];

        return round(
            ($components['attendance'] * 0.4) +
                ($components['assignments'] * 0.3) +
                ($components['tests'] * 0.3),
            1
        );
    }

    private function statusFromScore(float $score): array
    {
        if ($score >= 85) {
            return ['label' => 'Excellent', 'state' => 'excellent'];
        }

        if ($score >= 70) {
            return ['label' => 'On Track', 'state' => 'on-track'];
        }

        if ($score >= 50) {
            return ['label' => 'Needs Attention', 'state' => 'warning'];
        }

        return ['label' => 'At Risk', 'state' => 'risk'];
    }
}

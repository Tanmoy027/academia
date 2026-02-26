<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $teachersQuery = User::query()
            ->where('role', 'teacher')
            ->withCount([
                'teachingCourses as total_courses_count',
                'teachingCourses as active_courses_count' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->with(['teachingCourses' => function ($query) {
                $query->select('id', 'teacher_id', 'title', 'image_url', 'price', 'start_date', 'is_active')
                    ->where('is_active', true)
                    ->orderBy('start_date')
                    ->take(3);
            }])
            ->orderByDesc('active_courses_count')
            ->orderBy('name');

        if ($search !== '') {
            $teachersQuery->where(function ($query) use ($search) {
                $like = '%' . strtolower($search) . '%';
                $query->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(specialization) LIKE ?', [$like]);
            });
        }

        $teachers = $teachersQuery->paginate(9)->withQueryString();

        return view('teachers.index', [
            'teachers' => $teachers,
            'search' => $search,
        ]);
    }

    public function show(User $teacher): View
    {
        abort_if($teacher->role !== 'teacher', 404);

        $teacher->load(['teachingCourses' => function ($query) {
            $query->select('id', 'teacher_id', 'title', 'description', 'image_url', 'price', 'start_date', 'end_date', 'is_active')
                ->orderByDesc('is_active')
                ->orderBy('start_date');
        }]);

        $courseIds = $teacher->teachingCourses->pluck('id');
        $studentsTaught = $courseIds->isEmpty()
            ? 0
            : Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id');

        $stats = [
            'total_courses' => $teacher->teachingCourses->count(),
            'active_courses' => $teacher->teachingCourses->where('is_active', true)->count(),
            'students_taught' => $studentsTaught,
        ];

        return view('teachers.show', [
            'teacher' => $teacher,
            'stats' => $stats,
        ]);
    }
}

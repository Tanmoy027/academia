<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function home(): View
    {
        $stats = [
            'courses' => Course::count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'enrollments' => Enrollment::count(),
        ];

        $upcomingCourses = Course::orderBy('start_date')
            ->take(3)
            ->get();

        $featuredTeachers = User::where('role', 'teacher')
            ->withCount('teachingCourses')
            ->orderByDesc('teaching_courses_count')
            ->take(3)
            ->get();

        return view('welcome', [
            'stats' => $stats,
            'upcomingCourses' => $upcomingCourses,
            'featuredTeachers' => $featuredTeachers,
        ]);
    }

    public function about(): View
    {
        $milestones = [
            [
                'year' => '2018',
                'title' => 'The Vision',
                'detail' => 'A small instructor collective sets out to build a human-first learning platform.',
            ],
            [
                'year' => '2020',
                'title' => 'Platform Launch',
                'detail' => 'Academia opens to the public with live classrooms and automated grading.',
            ],
            [
                'year' => '2022',
                'title' => 'Global Classrooms',
                'detail' => 'Teachers from 25+ countries host their cohorts using our real-time toolkit.',
            ],
            [
                'year' => 'Today',
                'title' => 'Learning OS',
                'detail' => 'We operate as the operating system for modern academies, blending analytics with community.',
            ],
        ];

        $values = [
            [
                'icon' => 'bi-lightning-charge',
                'title' => 'Speed with Soul',
                'detail' => 'Ship quickly, but never at the cost of trust or inclusion.',
            ],
            [
                'icon' => 'bi-people',
                'title' => 'Collective Intelligence',
                'detail' => 'Students, teachers, and staff co-create every workflow.',
            ],
            [
                'icon' => 'bi-shield-check',
                'title' => 'Dependable Tech',
                'detail' => 'Secure infrastructure that scales with growing campuses.',
            ],
        ];

        return view('about', [
            'milestones' => $milestones,
            'values' => $values,
        ]);
    }
}

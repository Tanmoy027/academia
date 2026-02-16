<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * Display notices for students.
     */
    public function studentNotices(Request $request)
    {
        $query = Notice::forRole('student')->latest();

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $notices = $query->paginate(10);

        return view('student.notices', compact('notices'));
    }

    /**
     * Display notices for teachers.
     */
    public function teacherNotices(Request $request)
    {
        $query = Notice::forRole('teacher')->latest();

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $notices = $query->paginate(10);

        return view('teacher.notices', compact('notices'));
    }
}

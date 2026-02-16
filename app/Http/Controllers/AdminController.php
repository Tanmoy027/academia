<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_courses' => Course::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'recent_enrollments' => Enrollment::with(['user', 'course'])->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // --- User Management ---
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => 'required|in:admin,teacher,student',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    // --- Course Management ---
    public function courses()
    {
        $courses = Course::with('teacher')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.courses.create', compact('teachers'));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'days' => 'nullable|array',
            'time' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $schedule = null;
        if ($request->has('days') && count($request->days) > 0) {
            $schedule = implode(', ', $request->days);
            if ($request->filled('time')) {
                $schedule .= ' at ' . date('h:i A', strtotime($request->time));
            }
        } elseif ($request->filled('time')) {
            $schedule = 'Daily at ' . date('h:i A', strtotime($request->time));
        }

        $validated['schedule'] = $schedule;
        $validated['is_active'] = $request->has('is_active');

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('courses', $filename, 's3');

            if ($path) {
                // Construct Supabase public URL manually
                $supabaseUrl = rtrim(env('AWS_URL', ''), '/');
                $validated['image_url'] = $supabaseUrl . '/' . $path;
            } else {
                return back()->withInput()->with('error', 'Failed to upload image. Please try again.');
            }
        }

        Course::create($validated);
        return redirect()->route('admin.courses')->with('success', 'Course created successfully.');
    }

    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
            'schedule' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image_url) {
                $oldPath = str_replace(rtrim(env('AWS_URL', ''), '/') . '/', '', $course->image_url);
                Storage::disk('s3')->delete($oldPath);
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('courses', $filename, 's3');

            if ($path) {
                // Construct Supabase public URL manually
                $supabaseUrl = rtrim(env('AWS_URL', ''), '/');
                $validated['image_url'] = $supabaseUrl . '/' . $path;
            } else {
                return back()->withInput()->with('error', 'Failed to upload image. Please try again.');
            }
        }

        $course->update($validated);

        return redirect()->route('admin.courses')->with('success', 'Course updated successfully.');
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully.');
    }

    // --- Notice Management ---
    public function notices()
    {
        $notices = Notice::latest()->paginate(10);
        $categories = Notice::getCategories();
        return view('admin.notices.index', compact('notices', 'categories'));
    }

    public function storeNotice(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|in:general,exam,holiday,class_update,event,urgent',
            'target_role' => 'required|string|in:all,student,teacher',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Notice::create($validated);
        return back()->with('success', 'Notice posted successfully.');
    }

    public function editNotice($id)
    {
        $notice = Notice::findOrFail($id);
        $categories = Notice::getCategories();
        return view('admin.notices.edit', compact('notice', 'categories'));
    }

    public function updateNotice(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|in:general,exam,holiday,class_update,event,urgent',
            'target_role' => 'required|string|in:all,student,teacher',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $notice->update($validated);
        return redirect()->route('admin.notices')->with('success', 'Notice updated successfully.');
    }

    public function deleteNotice($id)
    {
        $notice = Notice::findOrFail($id);
        $notice->delete();
        return redirect()->route('admin.notices')->with('success', 'Notice deleted successfully.');
    }
}

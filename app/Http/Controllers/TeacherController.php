<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseMaterial;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\StudentQuizAttempt;
use App\Models\StudentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\UserProfilePhotoService;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function __construct(private readonly UserProfilePhotoService $photoService) {}

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
            ->with([
                'students',
                'materials' => fn($query) => $query->latest()->limit(1),
            ])
            ->withCount('materials')
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
            ->with(['materials' => fn($query) => $query->latest()])
            ->firstOrFail();

        $materials = $course->materials;
        $stats = $this->materialStats($materials);

        return view('teacher.courses.materials', [
            'course' => $course,
            'materials' => $materials,
            'stats' => $stats,
        ]);
    }

    public function storeCourseMaterial(Request $request, $id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,csv,zip,rar,txt,rtf,mp4,mov,wmv,avi,mkv,webm,mp3,wav,jpg,jpeg,png',
        ]);

        $file = $request->file('file');
        $disk = $this->courseMaterialDisk();
        $directory = 'courses/' . $course->id . '/materials';
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString() . ($extension ? '.' . $extension : '');

        $path = Storage::disk($disk)->putFileAs($directory, $file, $filename, [
            'visibility' => 'public',
        ]);

        $material = CourseMaterial::create([
            'course_id' => $course->id,
            'uploaded_by' => $teacher->id,
            'title' => $validated['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_url' => Storage::disk($disk)->url($path),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()
            ->route('teacher.courses.materials', $course->id)
            ->with('success', 'Material "' . $material->title . '" uploaded successfully.');
    }

    public function deleteCourseMaterial($courseId, $materialId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $material = CourseMaterial::where('course_id', $course->id)
            ->where('id', $materialId)
            ->firstOrFail();

        Storage::disk($this->courseMaterialDisk())->delete($material->file_path);
        $title = $material->title;
        $material->delete();

        return redirect()
            ->route('teacher.courses.materials', $course->id)
            ->with('success', 'Material "' . $title . '" deleted successfully.');
    }

    /**
     * Share or clear the live session link for students.
     */
    public function updateLiveSessionLink(Request $request, $id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $validated = $request->validate([
            'live_session_link' => 'nullable|url|max:2048',
            'live_session_message' => 'nullable|string|max:500',
            'expires_in' => 'nullable|integer|min:15|max:480',
            'clear_link' => 'nullable|boolean',
        ]);

        if ($request->boolean('clear_link')) {
            $course->update([
                'live_session_link' => null,
                'live_session_message' => null,
                'live_session_expires_at' => null,
            ]);

            return back()->with('success', 'Live session link cleared for students.');
        }

        if (empty($validated['live_session_link'])) {
            return back()->with('error', 'Please provide a valid URL before sharing the live session link.');
        }

        $course->update([
            'live_session_link' => $validated['live_session_link'],
            'live_session_message' => $validated['live_session_message'] ?? null,
            'live_session_expires_at' => Carbon::now()->addMinutes((int) ($validated['expires_in'] ?? 120)),
        ]);

        return back()->with('success', 'Live session link shared with enrolled students.');
    }

    /**
     * Display attendance records for a course
     */
    public function courseAttendance($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        // Get all attendance records for this course, grouped by date
        $attendances = Attendance::where('course_id', $id)
            ->with('student')
            ->orderBy('date', 'desc')
            ->orderBy('type')
            ->get()
            ->groupBy(function ($item) {
                return $item->date->format('Y-m-d') . '_' . $item->type;
            });

        return view('teacher.attendance.index', compact('course', 'attendances'));
    }

    /**
     * Show form to mark attendance for a specific date and type
     */
    public function markAttendance($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        $students = $course->students;
        $types = Attendance::getTypes();
        $statuses = Attendance::getStatuses();

        return view('teacher.attendance.mark', compact('course', 'students', 'types', 'statuses'));
    }

    /**
     * Store attendance records
     */
    public function storeAttendance(Request $request, $id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:regular,makeup,quiz',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:500',
        ]);

        foreach ($validated['attendance'] as $userId => $status) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $userId,
                    'course_id' => $course->id,
                    'date' => $validated['date'],
                    'type' => $validated['type'],
                ],
                [
                    'status' => $status,
                    'notes' => $validated['notes'][$userId] ?? null,
                ]
            );
        }

        return redirect()
            ->route('teacher.courses.attendance', $course->id)
            ->with('success', 'Attendance marked successfully!');
    }

    /**
     * Show attendance for a specific date and type
     */
    public function showAttendance($id, Request $request)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $date = $request->input('date');
        $type = $request->input('type');

        $attendances = Attendance::where('course_id', $id)
            ->where('date', $date)
            ->where('type', $type)
            ->with('student')
            ->get();

        return view('teacher.attendance.show', compact('course', 'attendances', 'date', 'type'));
    }

    /**
     * Edit attendance for a specific date and type
     */
    public function editAttendance($id, Request $request)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('students')
            ->firstOrFail();

        $date = $request->input('date');
        $type = $request->input('type');

        $students = $course->students;
        $types = Attendance::getTypes();
        $statuses = Attendance::getStatuses();

        // Get existing attendance records
        $existingAttendance = Attendance::where('course_id', $id)
            ->where('date', $date)
            ->where('type', $type)
            ->get()
            ->keyBy('user_id');

        return view('teacher.attendance.edit', compact('course', 'students', 'types', 'statuses', 'date', 'type', 'existingAttendance'));
    }

    /**
     * Get attendance statistics for a student in a course
     */
    public function studentAttendanceStats($courseId, $studentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $student = User::findOrFail($studentId);

        // Check if student is enrolled
        $enrollment = Enrollment::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->firstOrFail();

        $attendances = Attendance::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'by_type' => [
                'regular' => $attendances->where('type', 'regular')->count(),
                'makeup' => $attendances->where('type', 'makeup')->count(),
                'quiz' => $attendances->where('type', 'quiz')->count(),
            ]
        ];

        return view('teacher.attendance.student-stats', compact('course', 'student', 'attendances', 'stats'));
    }

    /**
     * Display the teacher's profile
     */
    public function profile()
    {
        $teacher = Auth::user();
        $coursesCount = $teacher->teachingCourses()->count();
        $studentsCount = Enrollment::whereIn('course_id', $teacher->teachingCourses()->pluck('id'))->distinct('user_id')->count();

        return view('teacher.profile.index', compact('teacher', 'coursesCount', 'studentsCount'));
    }

    /**
     * Show the form for editing the teacher's profile
     */
    public function editProfile()
    {
        $teacher = Auth::user();
        return view('teacher.profile.edit', compact('teacher'));
    }

    /**
     * Update the teacher's profile
     */
    public function updateProfile(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'qualification' => 'nullable|string|max:1000',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:99',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        // Handle social links
        $socialLinks = [];
        if ($request->filled('linkedin')) {
            $socialLinks['linkedin'] = $request->linkedin;
        }
        if ($request->filled('twitter')) {
            $socialLinks['twitter'] = $request->twitter;
        }
        if ($request->filled('facebook')) {
            $socialLinks['facebook'] = $request->facebook;
        }
        if ($request->filled('website')) {
            $socialLinks['website'] = $request->website;
        }

        $teacher->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'social_links' => !empty($socialLinks) ? $socialLinks : null,
        ]);

        return redirect()
            ->route('teacher.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $this->photoService->store($teacher, $request->file('profile_picture'));

            $teacher->update([
                'profile_picture' => $path,
            ]);

            return redirect()
                ->route('teacher.profile')
                ->with('success', 'Profile picture updated successfully!');
        }

        return redirect()
            ->route('teacher.profile')
            ->with('error', 'Failed to upload profile picture.');
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture()
    {
        $teacher = Auth::user();

        if ($teacher->profile_picture) {
            $this->photoService->delete($teacher);

            return redirect()
                ->route('teacher.profile')
                ->with('success', 'Profile picture deleted successfully!');
        }

        return redirect()
            ->route('teacher.profile')
            ->with('error', 'No profile picture to delete.');
    }

    /**
     * Display assignments for a course
     */
    public function courseAssignments($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignments = Assignment::where('course_id', $id)
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.assignments.index', compact('course', 'assignments'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function createAssignment($id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        return view('teacher.assignments.create', compact('course'));
    }

    /**
     * Store a newly created assignment
     */
    public function storeAssignment(Request $request, $id)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_points' => 'required|integer|min:1|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,zip|max:10240',
        ]);

        $attachmentUrl = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'assignment_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $attachmentUrl = '/storage/' . $path;
        }

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'max_points' => $validated['max_points'],
            'attachment_url' => $attachmentUrl,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()
            ->route('teacher.courses.assignments', $course->id)
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Display a specific assignment with submissions
     */
    public function showAssignment($courseId, $assignmentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignment = Assignment::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->with(['submissions.student'])
            ->firstOrFail();

        // Get all enrolled students
        $enrolledStudents = $course->students;

        // Get submissions
        $submissions = $assignment->submissions;

        // Create a list of students who haven't submitted
        $submittedUserIds = $submissions->pluck('user_id')->toArray();
        $notSubmittedStudents = $enrolledStudents->reject(function ($student) use ($submittedUserIds) {
            return in_array($student->id, $submittedUserIds);
        });

        return view('teacher.assignments.show', compact('course', 'assignment', 'submissions', 'notSubmittedStudents'));
    }

    /**
     * Show the form for editing an assignment
     */
    public function editAssignment($courseId, $assignmentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignment = Assignment::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        return view('teacher.assignments.edit', compact('course', 'assignment'));
    }

    /**
     * Update an assignment
     */
    public function updateAssignment(Request $request, $courseId, $assignmentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignment = Assignment::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_points' => 'required|integer|min:1|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,zip|max:10240',
        ]);

        $attachmentUrl = $assignment->attachment_url;

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($assignment->attachment_url) {
                $oldPath = str_replace('/storage/', '', $assignment->attachment_url);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('attachment');
            $filename = 'assignment_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $attachmentUrl = '/storage/' . $path;
        }

        $assignment->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'max_points' => $validated['max_points'],
            'attachment_url' => $attachmentUrl,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()
            ->route('teacher.assignments.show', ['courseId' => $course->id, 'assignmentId' => $assignment->id])
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Delete an assignment
     */
    public function deleteAssignment($courseId, $assignmentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignment = Assignment::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Delete attachment file if exists
        if ($assignment->attachment_url) {
            $path = str_replace('/storage/', '', $assignment->attachment_url);
            Storage::disk('public')->delete($path);
        }

        $assignment->delete();

        return redirect()
            ->route('teacher.courses.assignments', $course->id)
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Delete assignment attachment
     */
    public function deleteAssignmentAttachment($courseId, $assignmentId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignment = Assignment::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        if ($assignment->attachment_url) {
            $path = str_replace('/storage/', '', $assignment->attachment_url);
            Storage::disk('public')->delete($path);

            $assignment->update(['attachment_url' => null]);

            return redirect()
                ->back()
                ->with('success', 'Attachment deleted successfully!');
        }

        return redirect()
            ->back()
            ->with('error', 'No attachment to delete.');
    }

    // ======================== QUIZ METHODS ========================

    /**
     * Display all quizzes for a course
     */
    public function courseQuizzes($courseId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quizzes = Quiz::where('course_id', $courseId)
            ->with('questions', 'studentAttempts')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('teacher.quizzes.index', compact('course', 'quizzes'));
    }

    /**
     * Show create quiz form
     */
    public function createQuiz($courseId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        return view('teacher.quizzes.create', compact('course'));
    }

    /**
     * Store a newly created quiz
     */
    public function storeQuiz(Request $request, $courseId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:1|max:1000',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'show_correct_answers' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
        ]);

        $validated['course_id'] = $courseId;
        $validated['teacher_id'] = $teacher->id;
        $validated['is_published'] = false;
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['shuffle_questions'] = $request->has('shuffle_questions');

        $quiz = Quiz::create($validated);

        return redirect()
            ->route('teacher.quiz.show', ['courseId' => $courseId, 'quizId' => $quiz->id])
            ->with('success', 'Quiz created successfully! Now add questions to your quiz.');
    }

    /**
     * Show quiz details with questions
     */
    public function showQuiz($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->with('questions.options')
            ->firstOrFail();

        $questions = $quiz->questions()->orderBy('order')->get();

        return view('teacher.quizzes.show', compact('course', 'quiz', 'questions'));
    }

    /**
     * Add a question to quiz (display form)
     */
    public function createQuestion($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        return view('teacher.quizzes.add-question', compact('course', 'quiz'));
    }

    /**
     * Store a question
     */
    public function storeQuestion(Request $request, $courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,short_answer',
            'marks' => 'required|integer|min:1|max:100',
            'options' => 'required_if:question_type,mcq|array|min:2',
            'options.*' => 'required_if:question_type,mcq|string|max:500',
            'correct_option' => 'required_if:question_type,mcq|integer',
        ]);

        $order = $quiz->questions()->max('order') + 1;

        $question = Question::create([
            'quiz_id' => $quizId,
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'marks' => $validated['marks'],
            'order' => $order,
        ]);

        // Add options for MCQ
        if ($validated['question_type'] === 'mcq' && isset($validated['options'])) {
            foreach ($validated['options'] as $index => $optionText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => $index == $validated['correct_option'],
                ]);
            }
        }

        return redirect()
            ->route('teacher.quiz.show', ['courseId' => $courseId, 'quizId' => $quizId])
            ->with('success', 'Question added successfully!');
    }

    /**
     * Edit a question
     */
    public function editQuestion($courseId, $quizId, $questionId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $question = Question::where('id', $questionId)
            ->where('quiz_id', $quizId)
            ->with('options')
            ->firstOrFail();

        return view('teacher.quizzes.edit-question', compact('course', 'quiz', 'question'));
    }

    /**
     * Update a question
     */
    public function updateQuestion(Request $request, $courseId, $quizId, $questionId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $question = Question::where('id', $questionId)
            ->where('quiz_id', $quizId)
            ->firstOrFail();

        $validated = $request->validate([
            'question_text' => 'required|string',
            'marks' => 'required|integer|min:1|max:100',
            'options' => 'array|min:2',
            'options.*' => 'string|max:500',
            'correct_option' => 'integer',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'marks' => $validated['marks'],
        ]);

        // Update options for MCQ
        if ($question->isMCQ() && isset($validated['options'])) {
            $question->options()->delete();

            foreach ($validated['options'] as $index => $optionText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => $index == $validated['correct_option'],
                ]);
            }
        }

        return redirect()
            ->route('teacher.quiz.show', ['courseId' => $courseId, 'quizId' => $quizId])
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Delete a question
     */
    public function deleteQuestion($courseId, $quizId, $questionId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $question = Question::where('id', $questionId)
            ->where('quiz_id', $quizId)
            ->firstOrFail();

        $question->delete();

        return redirect()
            ->back()
            ->with('success', 'Question deleted successfully!');
    }

    /**
     * Edit quiz settings
     */
    public function editQuiz($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        return view('teacher.quizzes.edit', compact('course', 'quiz'));
    }

    /**
     * Update quiz settings
     */
    public function updateQuiz(Request $request, $courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:1|max:1000',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'show_correct_answers' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
        ]);

        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['shuffle_questions'] = $request->has('shuffle_questions');

        $quiz->update($validated);

        return redirect()
            ->route('teacher.quiz.show', ['courseId' => $courseId, 'quizId' => $quizId])
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Publish a quiz
     */
    public function publishQuiz($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Validate quiz has at least one question
        if ($quiz->questions()->count() === 0) {
            return redirect()
                ->back()
                ->with('error', 'Quiz must have at least one question before publishing!');
        }

        $quiz->update(['is_published' => true]);

        return redirect()
            ->back()
            ->with('success', 'Quiz published successfully!');
    }

    /**
     * Unpublish a quiz
     */
    public function unpublishQuiz($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $quiz->update(['is_published' => false]);

        return redirect()
            ->back()
            ->with('success', 'Quiz unpublished successfully!');
    }

    /**
     * Delete a quiz
     */
    public function deleteQuiz($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $quiz->delete();

        return redirect()
            ->route('teacher.course.quizzes', ['courseId' => $courseId])
            ->with('success', 'Quiz deleted successfully!');
    }

    /**
     * View quiz results
     */
    public function quizResults($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->with('studentAttempts.student')
            ->firstOrFail();

        $attempts = $quiz->studentAttempts()
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $statistics = $quiz->getStatistics();

        return view('teacher.quizzes.results', compact('course', 'quiz', 'attempts', 'statistics'));
    }

    /**
     * View individual student's quiz attempt result
     */
    public function viewStudentQuizAttempt($courseId, $quizId, $attemptId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $attempt = StudentQuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->with('student', 'responses.question.options', 'responses.selectedOption')
            ->firstOrFail();

        $responses = $attempt->responses()
            ->with('question.options', 'selectedOption')
            ->get();

        return view('teacher.quizzes.student-attempt', compact('course', 'quiz', 'attempt', 'responses'));
    }

    /**
     * Grade short answer response
     */
    public function gradeResponse(Request $request, $courseId, $quizId, $attemptId, $responseId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $attempt = StudentQuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->firstOrFail();

        $response = StudentResponse::where('id', $responseId)
            ->where('student_quiz_attempt_id', $attemptId)
            ->with('question')
            ->firstOrFail();

        $validated = $request->validate([
            'marks_awarded' => 'required|numeric|min:0|max:' . $response->question->marks,
            'teacher_feedback' => 'nullable|string',
        ]);

        $response->update($validated);

        // Recalculate total marks for attempt
        $attempt->calculateMarks();

        return redirect()
            ->back()
            ->with('success', 'Response graded successfully!');
    }

    /**
     * View class performance
     */
    public function classPerformance($courseId, $quizId)
    {
        $teacher = Auth::user();
        $course = Course::where('id', $courseId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $quiz = Quiz::where('id', $quizId)
            ->where('course_id', $courseId)
            ->with('studentAttempts.student')
            ->firstOrFail();

        $attempts = $quiz->studentAttempts()
            ->with('student')
            ->where('is_submitted', true)
            ->orderBy('percentage', 'desc')
            ->get();

        $statistics = $quiz->getStatistics();

        return view('teacher.quizzes.performance', compact('course', 'quiz', 'attempts', 'statistics'));
    }

    private function materialStats($materials): array
    {
        $totals = [
            'documents' => 0,
            'videos' => 0,
            'others' => 0,
            'count' => $materials->count(),
            'size_bytes' => 0,
        ];

        foreach ($materials as $material) {
            $category = $this->detectMaterialCategory($material);
            $totals[$category]++;
            $totals['size_bytes'] += $material->file_size ?? 0;
        }

        $totals['size_human'] = $this->formatBytes((int) $totals['size_bytes']);

        return $totals;
    }

    private function detectMaterialCategory(CourseMaterial $material): string
    {
        $mime = strtolower($material->file_type ?? '');
        $extension = $material->file_extension ?? '';

        $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'mkv', 'webm'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'zip', 'rar', 'jpg', 'jpeg', 'png'];

        if (str_contains($mime, 'video') || in_array($extension, $videoExtensions, true)) {
            return 'videos';
        }

        if (
            str_contains($mime, 'pdf') ||
            str_contains($mime, 'word') ||
            str_contains($mime, 'presentation') ||
            str_contains($mime, 'spreadsheet') ||
            str_contains($mime, 'text') ||
            in_array($extension, $documentExtensions, true)
        ) {
            return 'documents';
        }

        return 'others';
    }

    private function courseMaterialDisk(): string
    {
        return config('filesystems.course_material_disk', 'courseprofile');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}

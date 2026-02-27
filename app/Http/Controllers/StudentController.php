<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Course;
use App\Models\StudentQuizAttempt;
use App\Models\StudentResponse;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Display all available quizzes for enrolled courses
     */
    public function availableQuizzes()
    {
        $student = Auth::user();
        
        // Get all courses the student is enrolled in
        $enrolledCourses = $student->enrollments()
            ->where('status', 'active')
            ->pluck('course_id')
            ->toArray();

        // Get all quizzes for these courses
        $upcomingQuizzes = Quiz::whereIn('course_id', $enrolledCourses)
            ->where('is_published', true)
            ->where('start_time', '>', now())
            ->with('course', 'questions')
            ->orderBy('start_time', 'asc')
            ->get();

        $activeQuizzes = Quiz::whereIn('course_id', $enrolledCourses)
            ->where('is_published', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->with('course', 'questions')
            ->orderBy('start_time', 'asc')
            ->get();

        $completedQuizzes = Quiz::whereIn('course_id', $enrolledCourses)
            ->where('is_published', true)
            ->where('end_time', '<', now())
            ->with('course', 'questions')
            ->orderBy('end_time', 'desc')
            ->get();

        return view('student.quizzes.available', compact('upcomingQuizzes', 'activeQuizzes', 'completedQuizzes'));
    }

    /**
     * Start a quiz attempt
     */
    public function startQuiz($quizId)
    {
        $student = Auth::user();
        $quiz = Quiz::where('id', $quizId)
            ->where('is_published', true)
            ->firstOrFail();

        // Verify student is enrolled in the course
        $isEnrolled = $student->enrollments()
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Check if quiz is still available
        if (!$quiz->isActive() && !$quiz->hasStarted()) {
            return redirect()
                ->route('student.quizzes.available')
                ->with('error', 'This quiz has not started yet.');
        }

        if ($quiz->hasEnded()) {
            return redirect()
                ->route('student.quizzes.available')
                ->with('error', 'This quiz has ended.');
        }

        // Check if student has already submitted
        $existingAttempt = StudentQuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->first();

        if ($existingAttempt && $existingAttempt->is_submitted) {
            return redirect()
                ->route('student.quiz.result', ['quizId' => $quizId])
                ->with('info', 'You have already submitted this quiz.');
        }

        // Create new attempt if not exists or resume existing
        if (!$existingAttempt) {
            $existingAttempt = StudentQuizAttempt::create([
                'quiz_id' => $quizId,
                'student_id' => $student->id,
                'started_at' => now(),
            ]);
        }

        // Load the quiz with questions
        $quiz->load('questions.options');
        $questions = $quiz->questions;

        // Shuffle questions if enabled
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.quizzes.attempt', compact('quiz', 'existingAttempt', 'questions'));
    }

    /**
     * Store a response to a question
     */
    public function saveResponse(Request $request, $quizId, $attemptId)
    {
        $student = Auth::user();
        $quiz = Quiz::findOrFail($quizId);
        $attempt = StudentQuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->firstOrFail();

        // Check if time has exceeded
        if ($attempt->hasTimeExceeded()) {
            return response()->json([
                'success' => false,
                'message' => 'Time exceeded for this quiz.',
            ], 400);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'nullable|exists:question_options,id',
            'answer_text' => 'nullable|string',
        ]);

        $question = Question::findOrFail($validated['question_id']);

        // Find or create response
        $response = StudentResponse::updateOrCreate(
            [
                'student_quiz_attempt_id' => $attemptId,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Response saved successfully.',
        ]);
    }

    /**
     * Submit quiz
     */
    public function submitQuiz(Request $request, $quizId, $attemptId)
    {
        $student = Auth::user();
        $quiz = Quiz::findOrFail($quizId);
        $attempt = StudentQuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->firstOrFail();

        // Check if time has exceeded
        if ($attempt->hasTimeExceeded()) {
            // Auto-submit if time exceeded
        }

        // Mark as submitted
        $attempt->update([
            'is_submitted' => true,
            'submitted_at' => now(),
        ]);

        // Calculate marks
        $attempt->calculateMarks();

        return redirect()
            ->route('student.quiz.result', ['quizId' => $quizId])
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * View quiz result
     */
    public function viewQuizResult($quizId)
    {
        $student = Auth::user();
        $quiz = Quiz::where('id', $quizId)
            ->with('questions.options')
            ->firstOrFail();

        $attempt = StudentQuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->with('responses.question.options', 'responses.selectedOption')
            ->firstOrFail();

        $responses = $attempt->responses()
            ->with('question.options', 'selectedOption')
            ->get();

        return view('student.quizzes.result', compact('quiz', 'attempt', 'responses'));
    }

    /**
     * Get remaining time for quiz (AJAX)
     */
    public function getRemainingTime($attemptId)
    {
        $student = Auth::user();
        $attempt = StudentQuizAttempt::where('id', $attemptId)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $remainingSeconds = $attempt->getRemainingSeconds();
        $hasExceeded = $attempt->hasTimeExceeded();

        return response()->json([
            'remaining_seconds' => $remainingSeconds,
            'has_exceeded' => $hasExceeded,
            'time_spent_minutes' => $attempt->getTimeSpentMinutes(),
        ]);
    }

    /**
     * View all quiz results for a student
     */
    public function myQuizResults()
    {
        $student = Auth::user();
        $attempts = StudentQuizAttempt::where('student_id', $student->id)
            ->where('is_submitted', true)
            ->with('quiz.course')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('student.quizzes.my-results', compact('attempts'));
    }
}

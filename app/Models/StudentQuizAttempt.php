<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class StudentQuizAttempt extends Model
{
    protected $table = 'student_quiz_attempts';

    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'marks_obtained',
        'percentage',
        'is_submitted',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'marks_obtained' => 'float',
        'percentage' => 'float',
        'is_submitted' => 'boolean',
    ];

    /**
     * Get the quiz
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get all responses for this attempt
     */
    public function responses(): HasMany
    {
        return $this->hasMany(StudentResponse::class);
    }

    /**
     * Check if quiz time has exceeded
     */
    public function hasTimeExceeded(): bool
    {
        if (!$this->started_at) {
            return false;
        }

        $duration = $this->quiz->duration_minutes;
        $expiryTime = $this->started_at->addMinutes($duration);
        
        return Carbon::now()->isAfter($expiryTime);
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingSeconds(): int
    {
        if (!$this->started_at || $this->is_submitted) {
            return 0;
        }

        $duration = $this->quiz->duration_minutes;
        $expiryTime = $this->started_at->addMinutes($duration);
        $remaining = $expiryTime->diffInSeconds(Carbon::now(), false);

        return max(0, $remaining);
    }

    /**
     * Get time spent in minutes
     */
    public function getTimeSpentMinutes(): float
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->submitted_at ?? Carbon::now();
        return $this->started_at->diffInMinutes($endTime, false);
    }

    /**
     * Get student response to a specific question
     */
    public function getResponseToQuestion($questionId): ?StudentResponse
    {
        return $this->responses()
            ->where('question_id', $questionId)
            ->first();
    }

    /**
     * Calculate and store the marks
     */
    public function calculateMarks(): void
    {
        $totalMarks = 0;
        $responses = $this->responses()->with('question')->get();

        foreach ($responses as $response) {
            if ($response->question->isMCQ()) {
                // MCQ: Check if answer is correct
                $correctOption = $response->question->getCorrectOption();
                if ($response->selected_option_id == $correctOption?->id) {
                    $totalMarks += $response->question->marks;
                }
            } else if ($response->question->isShortAnswer()) {
                // Short answer: Teacher will manually grade
                // For now, check if teacher has graded it
                if ($response->marks_awarded !== null) {
                    $totalMarks += $response->marks_awarded;
                }
            }
        }

        $this->marks_obtained = $totalMarks;
        $this->percentage = round(($totalMarks / $this->quiz->total_marks) * 100, 2);
        $this->save();
    }

    /**
     * Check if attempt has been submitted
     */
    public function isSubmitted(): bool
    {
        return $this->is_submitted && $this->submitted_at !== null;
    }

    /**
     * Get pass status
     */
    public function isPassed(): bool
    {
        $passThreshold = $this->quiz->total_marks * 0.50; // 50% pass threshold
        return $this->marks_obtained >= $passThreshold;
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        if (!$this->is_submitted) {
            return 'warning';
        }

        return $this->isPassed() ? 'success' : 'danger';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        if (!$this->is_submitted) {
            return 'In Progress';
        }

        return $this->isPassed() ? 'Passed' : 'Failed';
    }

    /**
     * Scope: Submitted attempts
     */
    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    /**
     * Scope: In-progress attempts
     */
    public function scopeInProgress($query)
    {
        return $query->where('is_submitted', false);
    }

    /**
     * Scope: Passed attempts
     */
    public function scopePassed($query)
    {
        $passThreshold = \DB::raw('(select total_marks from quizzes where id = student_quiz_attempts.quiz_id) * 0.5');
        return $query->whereColumn('marks_obtained', '>=', $passThreshold);
    }
}

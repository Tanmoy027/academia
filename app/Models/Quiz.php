<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Quiz extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'title',
        'description',
        'total_marks',
        'duration_minutes',
        'start_time',
        'end_time',
        'show_correct_answers',
        'shuffle_questions',
        'is_published',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'show_correct_answers' => 'boolean',
        'shuffle_questions' => 'boolean',
        'is_published' => 'boolean',
        'total_marks' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Get the course that the quiz belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher who created the quiz
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get all questions for this quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get all student attempts for this quiz
     */
    public function studentAttempts(): HasMany
    {
        return $this->hasMany(StudentQuizAttempt::class);
    }

    /**
     * Check if quiz is currently active
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->is_published && 
               $now->isBetween($this->start_time, $this->end_time);
    }

    /**
     * Check if quiz has started
     */
    public function hasStarted(): bool
    {
        return Carbon::now()->isAfter($this->start_time);
    }

    /**
     * Check if quiz has ended
     */
    public function hasEnded(): bool
    {
        return Carbon::now()->isAfter($this->end_time);
    }

    /**
     * Check if a student has attempted this quiz
     */
    public function studentHasAttempted($studentId): bool
    {
        return $this->studentAttempts()
            ->where('student_id', $studentId)
            ->exists();
    }

    /**
     * Get student's quiz attempt
     */
    public function getStudentAttempt($studentId): ?StudentQuizAttempt
    {
        return $this->studentAttempts()
            ->where('student_id', $studentId)
            ->first();
    }

    /**
     * Get quiz statistics
     */
    public function getStatistics(): array
    {
        $attempts = $this->studentAttempts()->with('student')->get();
        
        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
            ];
        }

        $scores = $attempts->pluck('marks_obtained')->toArray();
        $passThreshold = $this->total_marks * 0.50; // 50% pass threshold
        $passCount = count(array_filter($scores, fn($score) => $score >= $passThreshold));

        return [
            'total_attempts' => count($scores),
            'average_score' => round(array_sum($scores) / count($scores), 2),
            'highest_score' => max($scores),
            'lowest_score' => min($scores),
            'pass_rate' => round(($passCount / count($scores)) * 100, 2),
        ];
    }

    /**
     * Scope: Published quizzes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: Unpublished quizzes
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope: Active quizzes
     */
    public function scopeActive($query)
    {
        return $query->where('is_published', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());
    }

    /**
     * Scope: Upcoming quizzes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_published', true)
            ->where('start_time', '>', now());
    }

    /**
     * Scope: Completed quizzes
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_published', true)
            ->where('end_time', '<', now());
    }
}

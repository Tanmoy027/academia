<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentResponse extends Model
{
    protected $table = 'student_responses';

    protected $fillable = [
        'student_quiz_attempt_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'marks_awarded',
        'teacher_feedback',
    ];

    protected $casts = [
        'marks_awarded' => 'float',
    ];

    /**
     * Get the student quiz attempt
     */
    public function studentQuizAttempt(): BelongsTo
    {
        return $this->belongsTo(StudentQuizAttempt::class);
    }

    /**
     * Get the question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the selected option (for MCQ)
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    /**
     * Check if response is correct (for MCQ)
     */
    public function isCorrect(): bool
    {
        if ($this->question->isMCQ()) {
            $correctOption = $this->question->getCorrectOption();
            return $this->selected_option_id == $correctOption?->id;
        }
        return false;
    }

    /**
     * Check if short answer response is graded
     */
    public function isGraded(): bool
    {
        return $this->question->isShortAnswer() && $this->marks_awarded !== null;
    }

    /**
     * Scope: Correct answers (MCQ only)
     */
    public function scopeCorrect($query)
    {
        return $query->whereHas('selectedOption', function ($q) {
            $q->where('is_correct', true);
        });
    }

    /**
     * Scope: Incorrect answers (MCQ only)
     */
    public function scopeIncorrect($query)
    {
        return $query->whereHas('selectedOption', function ($q) {
            $q->where('is_correct', false);
        });
    }

    /**
     * Scope: Graded short answers
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('marks_awarded');
    }

    /**
     * Scope: Ungraded short answers
     */
    public function scopeUngraded($query)
    {
        return $query->whereNull('marks_awarded');
    }
}

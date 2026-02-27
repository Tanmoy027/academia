<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    const TYPE_MCQ = 'mcq';
    const TYPE_SHORT_ANSWER = 'short_answer';

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'marks',
        'order',
    ];

    protected $casts = [
        'marks' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the quiz this question belongs to
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get all options for MCQ questions
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Get all student responses to this question
     */
    public function studentResponses(): HasMany
    {
        return $this->hasMany(StudentResponse::class);
    }

    /**
     * Get correct option(s) for this question
     */
    public function getCorrectOption()
    {
        if ($this->question_type === self::TYPE_MCQ) {
            return $this->options()->where('is_correct', true)->first();
        }
        return null;
    }

    /**
     * Check if question type is MCQ
     */
    public function isMCQ(): bool
    {
        return $this->question_type === self::TYPE_MCQ;
    }

    /**
     * Check if question type is short answer
     */
    public function isShortAnswer(): bool
    {
        return $this->question_type === self::TYPE_SHORT_ANSWER;
    }

    /**
     * Scope: MCQ questions
     */
    public function scopeMCQ($query)
    {
        return $query->where('question_type', self::TYPE_MCQ);
    }

    /**
     * Scope: Short answer questions
     */
    public function scopeShortAnswer($query)
    {
        return $query->where('question_type', self::TYPE_SHORT_ANSWER);
    }
}

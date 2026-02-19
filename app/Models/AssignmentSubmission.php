<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'submission_text',
        'attachment_url',
        'submitted_at',
        'grade',
        'feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'grade' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the assignment this submission belongs to
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student who submitted this
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the teacher who graded this submission
     */
    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Check if the submission is late
     */
    public function isLate(): bool
    {
        if (!$this->assignment->due_date || !$this->submitted_at) {
            return false;
        }
        
        return $this->submitted_at->gt($this->assignment->due_date);
    }

    /**
     * Check if the submission has been graded
     */
    public function isGraded(): bool
    {
        return $this->grade !== null;
    }

    /**
     * Get the status badge color
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->submitted_at) {
            return 'secondary';
        } elseif ($this->isGraded()) {
            return 'success';
        } elseif ($this->isLate()) {
            return 'warning';
        }
        return 'primary';
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->submitted_at) {
            return 'Not Submitted';
        } elseif ($this->isGraded()) {
            return 'Graded';
        } elseif ($this->isLate()) {
            return 'Late Submission';
        }
        return 'Submitted';
    }

    /**
     * Scope to filter graded submissions
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('grade');
    }

    /**
     * Scope to filter ungraded submissions
     */
    public function scopeUngraded($query)
    {
        return $query->whereNull('grade');
    }

    /**
     * Scope to filter by assignment
     */
    public function scopeForAssignment($query, $assignmentId)
    {
        return $query->where('assignment_id', $assignmentId);
    }

    /**
     * Scope to filter by student
     */
    public function scopeForStudent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter late submissions
     */
    public function scopeLate($query)
    {
        return $query->whereHas('assignment', function($q) {
            $q->whereNotNull('due_date');
        })->whereRaw('submitted_at > (SELECT due_date FROM assignments WHERE assignments.id = assignment_submissions.assignment_id)');
    }
}

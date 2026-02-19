<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'title',
        'description',
        'due_date',
        'max_points',
        'attachment_url',
        'is_published',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_published' => 'boolean',
        'max_points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the course this assignment belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher who created this assignment
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get all submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Check if the assignment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && Carbon::now()->gt($this->due_date);
    }

    /**
     * Check if the assignment is due soon (within 24 hours)
     */
    public function isDueSoon(): bool
    {
        if (!$this->due_date) {
            return false;
        }
        
        $now = Carbon::now();
        $dueSoonThreshold = Carbon::now()->addHours(24);
        
        return $this->due_date->gt($now) && $this->due_date->lte($dueSoonThreshold);
    }

    /**
     * Get the status badge color based on due date
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'danger';
        } elseif ($this->isDueSoon()) {
            return 'warning';
        }
        return 'success';
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'Overdue';
        } elseif ($this->isDueSoon()) {
            return 'Due Soon';
        }
        return 'Active';
    }

    /**
     * Get submission count
     */
    public function getSubmissionCountAttribute(): int
    {
        return $this->submissions()->count();
    }

    /**
     * Get pending submission count (not graded yet)
     */
    public function getPendingSubmissionCountAttribute(): int
    {
        return $this->submissions()->whereNull('grade')->count();
    }

    /**
     * Scope to filter published assignments
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to filter unpublished assignments
     */
    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope to filter by course
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope to filter overdue assignments
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now());
    }

    /**
     * Scope to filter upcoming assignments
     */
    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', Carbon::now());
    }
}

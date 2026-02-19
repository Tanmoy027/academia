<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'date',
        'type',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Attendance type constants
     */
    const TYPE_REGULAR = 'regular';
    const TYPE_MAKEUP = 'makeup';
    const TYPE_QUIZ = 'quiz';

    /**
     * Attendance status constants
     */
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_EXCUSED = 'excused';

    /**
     * Get available attendance types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_REGULAR => 'Regular Class',
            self::TYPE_MAKEUP => 'Make-up Class',
            self::TYPE_QUIZ => 'Quiz',
        ];
    }

    /**
     * Get available attendance statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_EXCUSED => 'Excused',
        ];
    }

    /**
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? 'Unknown';
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            self::STATUS_EXCUSED => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get the student (user) for this attendance record
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student (alias for user relationship)
     */
    public function student()
    {
        return $this->user();
    }

    /**
     * Get the course for this attendance record
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope to filter by course
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope to filter by student
     */
    public function scopeForStudent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Course extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'price',
        'teacher_id',
        'schedule',
        'is_active',
        'image_url',
        'start_date',
        'end_date',
        'class_type',
        'online_schedule',
        'offline_schedule',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Class type constants
     */
    const CLASS_TYPE_ONLINE = 'online';

    /**
     * Get available class types
     */
    public static function getClassTypes(): array
    {
        return [
            self::CLASS_TYPE_ONLINE => 'Online',
        ];
    }

    /**
     * Get the class type label
     */
    public function getClassTypeLabel(): string
    {
        return self::getClassTypes()[$this->class_type] ?? 'Online';
    }

    /**
     * Check if course is online
     */
    public function isOnline(): bool
    {
        return $this->class_type === self::CLASS_TYPE_ONLINE;
    }

    // Offline/hybrid class types have been removed, so no additional helpers are needed

    /**
     * Get enrolled students for this course
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all attendance records for this course
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all assignments for this course
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get all quizzes for this course
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Check if enrollment is allowed (course hasn't started yet)
     */
    public function canEnroll(): bool
    {
        if (!$this->start_date) {
            return true; // No start date set, enrollment allowed
        }
        return Carbon::now()->lt($this->start_date);
    }

    /**
     * Check if the course has started
     */
    public function hasStarted(): bool
    {
        if (!$this->start_date) {
            return false;
        }
        return Carbon::now()->gte($this->start_date);
    }

    /**
     * Check if the course has ended
     */
    public function hasEnded(): bool
    {
        if (!$this->end_date) {
            return false;
        }
        return Carbon::now()->gt($this->end_date);
    }

    /**
     * Get the course progress percentage (0-100)
     * Based on current date between start_date and end_date
     */
    public function getProgressPercentage(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $now = Carbon::now();
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        if ($now->lt($start)) {
            return 0; // Course hasn't started
        }

        if ($now->gt($end)) {
            return 100; // Course has ended
        }

        $totalDays = $start->diffInDays($end);
        if ($totalDays === 0) {
            return 100;
        }

        $elapsedDays = $start->diffInDays($now);
        return min(100, (int) round(($elapsedDays / $totalDays) * 100));
    }

    /**
     * Get days until course starts
     */
    public function getDaysUntilStart(): ?int
    {
        if (!$this->start_date) {
            return null;
        }

        $now = Carbon::now();
        $start = Carbon::parse($this->start_date);

        if ($now->gte($start)) {
            return 0; // Already started
        }

        return $now->diffInDays($start);
    }

    /**
     * Get the course status label
     */
    public function getStatusLabel(): string
    {
        if ($this->hasEnded()) {
            return 'Completed';
        }

        if ($this->hasStarted()) {
            return 'In Progress';
        }

        $daysUntil = $this->getDaysUntilStart();
        if ($daysUntil !== null && $daysUntil <= 7) {
            return "Starting in {$daysUntil} days";
        }

        return 'Upcoming';
    }

    /**
     * Get the calculated duration based on start and end dates
     * Returns a human-readable string like "10 Weeks" or "3 Months"
     */
    public function getCalculatedDuration(): ?string
    {
        if (!$this->start_date || !$this->end_date) {
            return $this->duration; // Fallback to manual duration if dates not set
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $totalDays = $start->diffInDays($end);

        if ($totalDays < 7) {
            return $totalDays . ' ' . ($totalDays === 1 ? 'Day' : 'Days');
        }

        $weeks = floor($totalDays / 7);
        if ($totalDays < 30) {
            return $weeks . ' ' . ($weeks === 1 ? 'Week' : 'Weeks');
        }

        $months = floor($totalDays / 30);
        $remainingWeeks = floor(($totalDays % 30) / 7);

        if ($months < 3 || $remainingWeeks > 0) {
            if ($remainingWeeks > 0) {
                return $months . ' ' . ($months === 1 ? 'Month' : 'Months') . ' ' . $remainingWeeks . ' ' . ($remainingWeeks === 1 ? 'Week' : 'Weeks');
            }
            return $months . ' ' . ($months === 1 ? 'Month' : 'Months');
        }

        return $months . ' Months';
    }

    /**
     * Accessor for duration - returns calculated duration if dates are set
     */
    public function getDurationAttribute($value): ?string
    {
        // If we have both dates, calculate duration automatically
        if ($this->start_date && $this->end_date) {
            return $this->getCalculatedDuration();
        }

        // Otherwise return the stored value
        return $value;
    }
}

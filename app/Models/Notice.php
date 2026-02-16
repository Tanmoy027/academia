<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'target_role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Category constants
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_EXAM = 'exam';
    const CATEGORY_HOLIDAY = 'holiday';
    const CATEGORY_CLASS_UPDATE = 'class_update';
    const CATEGORY_EVENT = 'event';
    const CATEGORY_URGENT = 'urgent';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_GENERAL => 'General',
            self::CATEGORY_EXAM => 'Exam',
            self::CATEGORY_HOLIDAY => 'Holiday',
            self::CATEGORY_CLASS_UPDATE => 'Class Update',
            self::CATEGORY_EVENT => 'Event',
            self::CATEGORY_URGENT => 'Urgent',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::getCategories()[$this->category] ?? 'General';
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_URGENT => 'danger',
            self::CATEGORY_EXAM => 'warning',
            self::CATEGORY_HOLIDAY => 'success',
            self::CATEGORY_CLASS_UPDATE => 'info',
            self::CATEGORY_EVENT => 'primary',
            default => 'secondary',
        };
    }

    // Scope to get notices for a specific role
    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('target_role', 'all')
                ->orWhere('target_role', $role);
        })->where('is_active', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'file_url',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function getFileExtensionAttribute(): ?string
    {
        $path = $this->file_path ?? '';

        if ($path === '') {
            return null;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return $extension ? strtolower($extension) : null;
    }
}

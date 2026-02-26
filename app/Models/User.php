<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'phone',
        'qualification',
        'specialization',
        'experience_years',
        'profile_picture',
        'social_links',
        'address',
        'city',
        'country',
        'stripe_connect_id',
        'stripe_account_status',
        'base_salary',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'experience_years' => 'integer',
            'base_salary' => 'decimal:2',
        ];
    }

    /**
     * Get the salary payments for this teacher
     */
    public function salaryPayments()
    {
        return $this->hasMany(\App\Models\TeacherSalary::class, 'teacher_id');
    }

    /**
     * Check if user has active Stripe Connect account
     */
    public function hasActiveStripeAccount(): bool
    {
        return $this->stripe_account_status === 'active' && !empty($this->stripe_connect_id);
    }

    /**
     * Get the courses taught by this teacher
     */
    public function teachingCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Get the enrollments for this student
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all attendance records for this user
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getProfilePictureUrlAttribute(): ?string
    {
        if (!$this->profile_picture) {
            return null;
        }

        if (Str::startsWith($this->profile_picture, ['http://', 'https://'])) {
            return $this->profile_picture;
        }

        if (Str::startsWith($this->profile_picture, '/storage/')) {
            return url($this->profile_picture);
        }

        $disk = config('filesystems.user_profile_disk', 'public');

        return Storage::disk($disk)->url($this->profile_picture);
    }
}

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'teacher') {
        return redirect()->route('teacher.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

    // Courses
    Route::get('/courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('admin.courses.create');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('admin.courses.store');
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('admin.courses.edit');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('admin.courses.update');
    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('admin.courses.delete');

    // Notices
    Route::get('/notices', [AdminController::class, 'notices'])->name('admin.notices');
    Route::post('/notices', [AdminController::class, 'storeNotice'])->name('admin.notices.store');
    Route::get('/notices/{id}/edit', [AdminController::class, 'editNotice'])->name('admin.notices.edit');
    Route::put('/notices/{id}', [AdminController::class, 'updateNotice'])->name('admin.notices.update');
    Route::delete('/notices/{id}', [AdminController::class, 'deleteNotice'])->name('admin.notices.delete');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
    Route::get('/notices', [NoticeController::class, 'teacherNotices'])->name('teacher.notices');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
    Route::get('/notices', [NoticeController::class, 'studentNotices'])->name('student.notices');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public Course Routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

// Payment Routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{course}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/checkout/{course}/process', [PaymentController::class, 'createCheckoutSession'])->name('payment.process');
    Route::get('/payment/{course}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{course}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

Route::get('/debug-image-url', function () {
    $course = \App\Models\Course::latest()->first();
    return [
        'id' => $course->id,
        'title' => $course->title,
        'image_url' => $course->image_url,
        'env_aws_url' => env('AWS_URL'),
        'config_filesystem_url' => config('filesystems.disks.s3.url'),
    ];
});

require __DIR__ . '/auth.php';

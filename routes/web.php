<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherSalaryController;
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

    // Teacher Salary Management
    Route::get('/salaries', [TeacherSalaryController::class, 'index'])->name('admin.salaries.index');
    Route::get('/salaries/create', [TeacherSalaryController::class, 'create'])->name('admin.salaries.create');
    Route::post('/salaries', [TeacherSalaryController::class, 'store'])->name('admin.salaries.store');
    Route::get('/salaries/{id}', [TeacherSalaryController::class, 'show'])->name('admin.salaries.show');
    Route::post('/salaries/{id}/process', [TeacherSalaryController::class, 'processPayment'])->name('admin.salaries.process');
    Route::patch('/salaries/{id}/cancel', [TeacherSalaryController::class, 'cancel'])->name('admin.salaries.cancel');
    Route::delete('/salaries/{id}', [TeacherSalaryController::class, 'destroy'])->name('admin.salaries.destroy');
    Route::get('/salaries-export', [TeacherSalaryController::class, 'export'])->name('admin.salaries.export');

    // Bulk Salary Payments
    Route::get('/salaries/bulk/create', [TeacherSalaryController::class, 'bulkPaymentForm'])->name('admin.salaries.bulk');
    Route::post('/salaries/bulk/process', [TeacherSalaryController::class, 'processBulkPayment'])->name('admin.salaries.bulk.process');

    // Stripe Connect for Teachers
    Route::get('/teachers/{id}/stripe-status', [TeacherSalaryController::class, 'teacherStripeStatus'])->name('admin.salaries.stripe.status');
    Route::get('/teachers/{id}/connect', [TeacherSalaryController::class, 'createConnectLink'])->name('admin.salaries.connect.create');
    Route::get('/teachers/{id}/connect/return', [TeacherSalaryController::class, 'connectReturn'])->name('admin.salaries.connect.return');
    Route::get('/teachers/{id}/connect/refresh', [TeacherSalaryController::class, 'connectRefresh'])->name('admin.salaries.connect.refresh');

    // Assign/Remove Stripe Account (for test mode)
    Route::post('/teachers/{id}/stripe-assign', [TeacherSalaryController::class, 'assignStripeAccount'])->name('admin.salaries.stripe.assign');
    Route::delete('/teachers/{id}/stripe-remove', [TeacherSalaryController::class, 'removeStripeAccount'])->name('admin.salaries.stripe.remove');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
    Route::get('/notices', [NoticeController::class, 'teacherNotices'])->name('teacher.notices');

    // Teacher Salary Management
    Route::get('/salary', [TeacherSalaryController::class, 'teacherSalaryHistory'])->name('teacher.salary');
    Route::get('/salary/connect', [TeacherSalaryController::class, 'teacherConnectStripe'])->name('teacher.salary.connect');
    Route::get('/salary/connect/return', [TeacherSalaryController::class, 'teacherConnectReturn'])->name('teacher.salary.connect.return');
    Route::get('/salary/connect/refresh', [TeacherSalaryController::class, 'teacherConnectRefresh'])->name('teacher.salary.connect.refresh');

    // Teacher Course Management
    Route::get('/courses', [TeacherController::class, 'courses'])->name('teacher.courses');
    Route::get('/courses/{id}', [TeacherController::class, 'showCourse'])->name('teacher.courses.show');
    Route::get('/courses/{id}/students', [TeacherController::class, 'courseStudents'])->name('teacher.courses.students');
    Route::get('/courses/{id}/materials', [TeacherController::class, 'courseMaterials'])->name('teacher.courses.materials');
    Route::get('/courses/{id}/performance', [PerformanceController::class, 'teacherCourse'])->name('teacher.courses.performance');
    Route::get('/courses/{courseId}/students/{studentId}/performance', [PerformanceController::class, 'teacherStudent'])->name('teacher.students.performance');

    // Attendance Management
    Route::get('/courses/{id}/attendance', [TeacherController::class, 'courseAttendance'])->name('teacher.courses.attendance');
    Route::get('/courses/{id}/attendance/mark', [TeacherController::class, 'markAttendance'])->name('teacher.attendance.mark');
    Route::post('/courses/{id}/attendance', [TeacherController::class, 'storeAttendance'])->name('teacher.attendance.store');
    Route::get('/courses/{id}/attendance/show', [TeacherController::class, 'showAttendance'])->name('teacher.attendance.show');
    Route::get('/courses/{id}/attendance/edit', [TeacherController::class, 'editAttendance'])->name('teacher.attendance.edit');
    Route::get('/courses/{id}/students/{studentId}/attendance', [TeacherController::class, 'studentAttendanceStats'])->name('teacher.attendance.student-stats');

    // Teacher Profile Management
    Route::get('/profile', [TeacherController::class, 'profile'])->name('teacher.profile');
    Route::get('/profile/edit', [TeacherController::class, 'editProfile'])->name('teacher.profile.edit');
    Route::put('/profile', [TeacherController::class, 'updateProfile'])->name('teacher.profile.update');
    Route::post('/profile/picture', [TeacherController::class, 'uploadProfilePicture'])->name('teacher.profile.picture.upload');
    Route::delete('/profile/picture', [TeacherController::class, 'deleteProfilePicture'])->name('teacher.profile.picture.delete');

    // Assignment Management
    Route::get('/courses/{id}/assignments', [TeacherController::class, 'courseAssignments'])->name('teacher.courses.assignments');
    Route::get('/courses/{id}/assignments/create', [TeacherController::class, 'createAssignment'])->name('teacher.assignments.create');
    Route::post('/courses/{id}/assignments', [TeacherController::class, 'storeAssignment'])->name('teacher.assignments.store');
    Route::get('/courses/{courseId}/assignments/{assignmentId}', [TeacherController::class, 'showAssignment'])->name('teacher.assignments.show');
    Route::get('/courses/{courseId}/assignments/{assignmentId}/edit', [TeacherController::class, 'editAssignment'])->name('teacher.assignments.edit');
    Route::put('/courses/{courseId}/assignments/{assignmentId}', [TeacherController::class, 'updateAssignment'])->name('teacher.assignments.update');
    Route::delete('/courses/{courseId}/assignments/{assignmentId}', [TeacherController::class, 'deleteAssignment'])->name('teacher.assignments.delete');
    Route::delete('/courses/{courseId}/assignments/{assignmentId}/attachment', [TeacherController::class, 'deleteAssignmentAttachment'])->name('teacher.assignments.attachment.delete');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
    Route::get('/notices', [NoticeController::class, 'studentNotices'])->name('student.notices');
    Route::get('/courses/{courseId}/performance', [PerformanceController::class, 'studentCourse'])->name('student.courses.performance');
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

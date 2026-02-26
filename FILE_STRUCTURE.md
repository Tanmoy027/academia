# 📁 Academia Project - File Location Guide

A comprehensive breakdown of the Laravel-based Academia project organized by user role (Admin, Teacher, Student) for both frontend and backend.

---

## 🔴 ADMIN Section

### Frontend (Views)

| File Location | Purpose |
|--------------|---------|
| `resources/views/admin/dashboard.blade.php` | Admin Dashboard |

#### Users Management
| File Location | Purpose |
|--------------|---------|
| `resources/views/admin/users/index.blade.php` | List all users |
| `resources/views/admin/users/create.blade.php` | Create new user form |

#### Courses Management
| File Location | Purpose |
|--------------|---------|
| `resources/views/admin/courses/index.blade.php` | List all courses |
| `resources/views/admin/courses/create.blade.php` | Create new course form |
| `resources/views/admin/courses/edit.blade.php` | Edit course form |

#### Notices Management
| File Location | Purpose |
|--------------|---------|
| `resources/views/admin/notices/index.blade.php` | List all notices |
| `resources/views/admin/notices/edit.blade.php` | Edit notice form |

### Backend (Controllers)

| File Location | Purpose |
|--------------|---------|
| `app/Http/Controllers/AdminController.php` | All admin operations |

### Routes (in `routes/web.php`)

| Route | Method | Function |
|-------|--------|----------|
| `/admin/dashboard` | GET | `admin.dashboard` |
| `/admin/users` | GET | List users |
| `/admin/users/create` | GET | Create user form |
| `/admin/users` | POST | Store new user |
| `/admin/courses` | GET | List courses |
| `/admin/courses/create` | GET | Create course form |
| `/admin/courses` | POST | Store course |
| `/admin/courses/{id}/edit` | GET | Edit course form |
| `/admin/courses/{id}` | PUT | Update course |
| `/admin/courses/{id}` | DELETE | Delete course |
| `/admin/notices` | GET | List notices |
| `/admin/notices` | POST | Store notice |
| `/admin/notices/{id}/edit` | GET | Edit notice |
| `/admin/notices/{id}` | PUT | Update notice |
| `/admin/notices/{id}` | DELETE | Delete notice |

---

## 🟢 TEACHER Section

### Frontend (Views)

| File Location | Purpose |
|--------------|---------|
| `resources/views/teacher/dashboard.blade.php` | Teacher Dashboard |
| `resources/views/teacher/notices.blade.php` | View notices |

#### Courses
| File Location | Purpose |
|--------------|---------|
| `resources/views/teacher/courses/index.blade.php` | List assigned courses |
| `resources/views/teacher/courses/show.blade.php` | View course details |
| `resources/views/teacher/courses/students.blade.php` | View enrolled students |
| `resources/views/teacher/courses/materials.blade.php` | Course materials |

#### Attendance
| File Location | Purpose |
|--------------|---------|
| `resources/views/teacher/attendance/index.blade.php` | Attendance overview |
| `resources/views/teacher/attendance/mark.blade.php` | Mark attendance |
| `resources/views/teacher/attendance/show.blade.php` | View attendance records |
| `resources/views/teacher/attendance/edit.blade.php` | Edit attendance |
| `resources/views/teacher/attendance/student-stats.blade.php` | Student attendance statistics |

#### Assignments
| File Location | Purpose |
|--------------|---------|
| `resources/views/teacher/assignments/index.blade.php` | List assignments |
| `resources/views/teacher/assignments/create.blade.php` | Create new assignment |
| `resources/views/teacher/assignments/edit.blade.php` | Edit assignment |
| `resources/views/teacher/assignments/show.blade.php` | View assignment details |

#### Profile
| File Location | Purpose |
|--------------|---------|
| `resources/views/teacher/profile/index.blade.php` | View profile |
| `resources/views/teacher/profile/edit.blade.php` | Edit profile |

### Backend (Controllers)

| File Location | Purpose |
|--------------|---------|
| `app/Http/Controllers/TeacherController.php` | Teacher-specific operations |
| `app/Http/Controllers/DashboardController.php` | Dashboard logic |
| `app/Http/Controllers/NoticeController.php` | Notice viewing |

### Routes (in `routes/web.php`)

| Route | Method | Function |
|-------|--------|----------|
| `/teacher/dashboard` | GET | Teacher dashboard |
| `/teacher/notices` | GET | View notices |
| `/teacher/courses` | GET | List courses |
| `/teacher/courses/{id}` | GET | Show course |
| `/teacher/courses/{id}/students` | GET | Course students |
| `/teacher/courses/{id}/materials` | GET | Course materials |
| `/teacher/courses/{id}/attendance` | GET | Attendance overview |
| `/teacher/courses/{id}/attendance/mark` | GET | Mark attendance form |
| `/teacher/courses/{id}/attendance` | POST | Store attendance |
| `/teacher/courses/{id}/attendance/show` | GET | Show attendance |
| `/teacher/courses/{id}/attendance/edit` | GET | Edit attendance |
| `/teacher/courses/{id}/students/{studentId}/attendance` | GET | Student attendance stats |
| `/teacher/courses/{id}/assignments` | GET | List assignments |
| `/teacher/courses/{id}/assignments/create` | GET | Create assignment |
| `/teacher/courses/{id}/assignments` | POST | Store assignment |
| `/teacher/courses/{courseId}/assignments/{assignmentId}` | GET | Show assignment |
| `/teacher/courses/{courseId}/assignments/{assignmentId}/edit` | GET | Edit assignment |
| `/teacher/courses/{courseId}/assignments/{assignmentId}` | PUT | Update assignment |
| `/teacher/courses/{courseId}/assignments/{assignmentId}` | DELETE | Delete assignment |
| `/teacher/profile` | GET | View profile |
| `/teacher/profile/edit` | GET | Edit profile form |
| `/teacher/profile` | PUT | Update profile |
| `/teacher/profile/picture` | POST | Upload profile picture |
| `/teacher/profile/picture` | DELETE | Delete profile picture |

---

## 🔵 STUDENT Section

### Frontend (Views)

| File Location | Purpose |
|--------------|---------|
| `resources/views/student/dashboard.blade.php` | Student Dashboard |
| `resources/views/student/notices.blade.php` | View notices |

### Backend (Controllers)

| File Location | Purpose |
|--------------|---------|
| `app/Http/Controllers/DashboardController.php` | Student dashboard logic |
| `app/Http/Controllers/NoticeController.php` | Student notices |
| `app/Http/Controllers/CourseController.php` | Browse courses |
| `app/Http/Controllers/PaymentController.php` | Course enrollment payment |

### Routes (in `routes/web.php`)

| Route | Method | Function |
|-------|--------|----------|
| `/student/dashboard` | GET | Student dashboard |
| `/student/notices` | GET | View notices |
| `/courses` | GET | Browse all courses (public) |
| `/courses/{id}` | GET | View course details (public) |
| `/checkout/{course}` | GET | Payment checkout |
| `/checkout/{course}/process` | POST | Process payment |
| `/payment/{course}/success` | GET | Payment success |
| `/payment/{course}/cancel` | GET | Payment cancelled |

---

## 🟣 SHARED / COMMON Files

### Frontend (Views)

| File Location | Purpose |
|--------------|---------|
| `resources/views/welcome.blade.php` | Landing page |
| `resources/views/dashboard.blade.php` | Redirect dashboard |

#### Layouts
| File Location | Purpose |
|--------------|---------|
| `resources/views/layouts/app.blade.php` | Main app layout |
| `resources/views/layouts/bootstrap.blade.php` | Bootstrap layout |
| `resources/views/layouts/guest.blade.php` | Guest/auth layout |
| `resources/views/layouts/navigation.blade.php` | Navigation component |

#### Courses (Public)
| File Location | Purpose |
|--------------|---------|
| `resources/views/courses/index.blade.php` | Course listing |
| `resources/views/courses/show.blade.php` | Course details |

#### Payments
| File Location | Purpose |
|--------------|---------|
| `resources/views/payments/checkout.blade.php` | Payment checkout page |

#### Authentication
| File Location | Purpose |
|--------------|---------|
| `resources/views/auth/login.blade.php` | Login page |
| `resources/views/auth/register.blade.php` | Registration page |
| `resources/views/auth/forgot-password.blade.php` | Forgot password |
| `resources/views/auth/reset-password.blade.php` | Reset password |
| `resources/views/auth/verify-email.blade.php` | Email verification |
| `resources/views/auth/confirm-password.blade.php` | Confirm password |

### Backend (Controllers)

| File Location | Purpose |
|--------------|---------|
| `app/Http/Controllers/Controller.php` | Base controller |
| `app/Http/Controllers/ProfileController.php` | User profile management |
| `app/Http/Controllers/CourseController.php` | Public course display |
| `app/Http/Controllers/PaymentController.php` | Stripe payment processing |

#### Auth Controllers
| File Location | Purpose |
|--------------|---------|
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Login/Logout |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | User registration |
| `app/Http/Controllers/Auth/PasswordController.php` | Password change |
| `app/Http/Controllers/Auth/PasswordResetLinkController.php` | Password reset link |
| `app/Http/Controllers/Auth/NewPasswordController.php` | New password |
| `app/Http/Controllers/Auth/EmailVerificationPromptController.php` | Email verification prompt |
| `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` | Resend verification |
| `app/Http/Controllers/Auth/VerifyEmailController.php` | Verify email |
| `app/Http/Controllers/Auth/ConfirmablePasswordController.php` | Confirm password |

---

## 🗃️ MODELS (Database)

| File Location | Purpose |
|--------------|---------|
| `app/Models/User.php` | User model (admin, teacher, student) |
| `app/Models/Course.php` | Course model |
| `app/Models/Enrollment.php` | Student enrollment |
| `app/Models/Assignment.php` | Assignments |
| `app/Models/AssignmentSubmission.php` | Student submissions |
| `app/Models/Attendance.php` | Attendance records |
| `app/Models/Notice.php` | Notices/announcements |
| `app/Models/Payment.php` | Payment records |

---

## 🔒 MIDDLEWARE

| File Location | Purpose |
|--------------|---------|
| `app/Http/Middleware/RoleMiddleware.php` | Role-based access control (admin/teacher/student) |

---

## 📁 ROUTES

| File Location | Purpose |
|--------------|---------|
| `routes/web.php` | All web routes |
| `routes/auth.php` | Authentication routes |
| `routes/console.php` | Console commands |

---

## 🗄️ DATABASE

### Migrations & Seeders
| File Location | Purpose |
|--------------|---------|
| `database/migrations/` | Database migrations |
| `database/seeders/AdminUserSeeder.php` | Admin user seeder |
| `database/seeders/DatabaseSeeder.php` | Main seeder |

### SQL Files
| File Location | Purpose |
|--------------|---------|
| `database/supabase_schema.sql` | Supabase schema |
| `database/supabase_notices.sql` | Notices table |
| `database/create_assignments_table.sql` | Assignments table |
| `database/create_assignment_submissions_table.sql` | Assignment submissions table |
| `database/create_attendances_table.sql` | Attendance table |
| `database/add_course_dates.sql` | Course dates migration |
| `database/add_teacher_profile_fields.sql` | Teacher profile fields |
| `database/admin_features.sql` | Admin features |
| `database/update_courses_table.sql` | Courses table updates |
| `database/update_courses_and_storage_rls.sql` | Storage RLS policies |
| `database/update_payments_stripe.sql` | Stripe payment fields |

---

## 📂 PROJECT STRUCTURE OVERVIEW

```
academia/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           # Authentication controllers
│   │   │   ├── AdminController.php
│   │   │   ├── CourseController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── NoticeController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ProfileController.php
│   │   │   └── TeacherController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── Assignment.php
│   │   ├── AssignmentSubmission.php
│   │   ├── Attendance.php
│   │   ├── Course.php
│   │   ├── Enrollment.php
│   │   ├── Notice.php
│   │   ├── Payment.php
│   │   └── User.php
│   ├── Providers/
│   └── View/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── *.sql files
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── admin/
│       │   ├── courses/
│       │   ├── notices/
│       │   ├── users/
│       │   └── dashboard.blade.php
│       ├── auth/
│       ├── components/
│       ├── courses/
│       ├── layouts/
│       ├── payments/
│       ├── profile/
│       ├── student/
│       ├── teacher/
│       │   ├── assignments/
│       │   ├── attendance/
│       │   ├── courses/
│       │   ├── profile/
│       │   ├── dashboard.blade.php
│       │   └── notices.blade.php
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
└── vendor/
```

---

## 🎯 Quick Reference

### Role-Based Access
- **Admin**: Full access to user management, course management, and notice management
- **Teacher**: Access to assigned courses, attendance, assignments, and profile
- **Student**: Access to dashboard, notices, course browsing, and enrollment

### Authentication Flow
1. User visits `/login` or `/register`
2. After authentication, redirected to `/dashboard`
3. Dashboard redirects based on role:
   - Admin → `/admin/dashboard`
   - Teacher → `/teacher/dashboard`
   - Student → `/student/dashboard`

### Key Technologies
- **Framework**: Laravel
- **Database**: Supabase (PostgreSQL)
- **Payment**: Stripe
- **Frontend**: Blade Templates, Tailwind CSS
- **Build Tool**: Vite

---

*This documentation was auto-generated for the Academia project.* 🎓

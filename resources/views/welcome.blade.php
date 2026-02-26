@extends('layouts.bootstrap')

@section('title', 'Academia — Learn Boldly')

@push('styles')
<style>
  .hero {
    background: radial-gradient(circle at top left, rgba(102, 126, 234, 0.35), transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(118, 75, 162, 0.35), transparent 55%),
      #0b0f19;
    color: #f8f9ff;
    border-radius: 32px;
    padding: 4rem;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
  }

  .hero h1 {
    font-size: clamp(2.4rem, 4vw, 3.5rem);
    font-weight: 700;
    margin-bottom: 1.5rem;
  }

  .hero p {
    max-width: 640px;
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.8);
  }

  .hero-cta .btn {
    border-radius: 14px;
    padding: 0.9rem 2.1rem;
    font-weight: 600;
  }

  .stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
  }

  .stat-card {
    background: #fff;
    border-radius: 22px;
    padding: 1.75rem;
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.18);
  }

  .stat-card span {
    display: block;
    font-size: 2.4rem;
    font-weight: 700;
    color: #4c1d95;
  }

  .feature-card,
  .community-card {
    border-radius: 22px;
    padding: 1.8rem;
    background: #fff;
    box-shadow: 0 15px 35px rgba(15, 32, 39, 0.08);
    height: 100%;
  }

  .feature-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(102, 126, 234, 0.1);
    color: #4c1d95;
    font-size: 1.5rem;
  }

  .section-heading {
    margin-bottom: 1.5rem;
    font-weight: 600;
  }

  .glass-panel {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(15, 32, 39, 0.15);
    padding: 2rem;
  }

  .course-pill,
  .teacher-pill {
    border-radius: 18px;
    padding: 0.4rem 0.9rem;
    font-size: 0.85rem;
    background: rgba(76, 29, 149, 0.08);
    margin-right: 0.3rem;
  }

  .cta-panel {
    border-radius: 28px;
    background: linear-gradient(135deg, #4c1d95, #2563eb);
    color: #fff;
    padding: 3rem;
    box-shadow: 0 35px 70px rgba(37, 99, 235, 0.35);
  }

  .cta-panel .btn {
    border-radius: 14px;
    padding: 0.95rem 2.5rem;
    font-weight: 600;
  }

  @media (max-width: 768px) {
    .hero {
      padding: 2.5rem;
    }

    .hero-cta {
      flex-direction: column;
      gap: 0.75rem;
      align-items: stretch;
    }
  }
</style>
@endpush

@php use Illuminate\Support\Str; @endphp

@section('content')
<section class="hero">
  <p class="text-uppercase fw-semibold text-white-50 mb-2">The operating system for modern academies</p>
  <h1>Design bold learning journeys with data, community, and creativity.</h1>
  <p class="mb-4">Academia brings teachers, students, and operations into one cinematic experience. Enroll cohorts,
    manage performance, and keep everyone inspired without switching tools.</p>
  <div class="d-flex align-items-center gap-3 hero-cta">
    @auth
    <a class="btn btn-light text-dark" href="{{ url('/dashboard') }}">
      <i class="bi bi-speedometer2 me-1"></i>Open Dashboard
    </a>
    @else
    <a class="btn btn-light text-dark" href="{{ route('register') }}">
      <i class="bi bi-rocket-takeoff me-1"></i>Launch Your Academy
    </a>
    <a class="btn btn-outline-light" href="{{ route('login') }}">I already have access</a>
    @endauth
    <a class="btn btn-outline-light" href="{{ route('courses.index') }}">
      Explore Courses
    </a>
  </div>
</section>

<section class="stat-grid">
  <div class="stat-card">
    <span>{{ number_format($stats['courses'] ?? 0) }}</span>
    Live courses orchestrated on Academia
  </div>
  <div class="stat-card">
    <span>{{ number_format($stats['teachers'] ?? 0) }}</span>
    Verified mentors building communities
  </div>
  <div class="stat-card">
    <span>{{ number_format($stats['students'] ?? 0) }}</span>
    Learners actively progressing
  </div>
  <div class="stat-card">
    <span>{{ number_format($stats['enrollments'] ?? 0) }}</span>
    Data-rich enrollments tracked this year
  </div>
</section>

<section class="mb-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon mb-3"><i class="bi bi-layers"></i></div>
        <h5>Unified Workflows</h5>
        <p class="text-muted">Attendance, assignments, payouts, and analytics flow through the same delightful interface.
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon mb-3"><i class="bi bi-graph-up"></i></div>
        <h5>Performance Intelligence</h5>
        <p class="text-muted">Spot trends per cohort with weighted performance snapshots, not spreadsheets.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon mb-3"><i class="bi bi-chat-dots"></i></div>
        <h5>Human-Centered Support</h5>
        <p class="text-muted">Students stay engaged through announcements, in-app messaging, and branded portals.</p>
      </div>
    </div>
  </div>
</section>

@if($upcomingCourses->isNotEmpty())
<section class="mb-5">
  <h3 class="section-heading">Upcoming experiences</h3>
  <div class="row g-4">
    @foreach($upcomingCourses as $course)
    <div class="col-md-4">
      <div class="glass-panel h-100">
        <div class="d-flex align-items-center mb-3">
          <div class="feature-icon me-3" style="background: rgba(37,99,235,0.12); color:#2563eb;">
            <i class="bi bi-play-circle"></i>
          </div>
          <div>
            <p class="text-muted mb-0 text-uppercase" style="font-size:0.75rem;">{{ $course->start_date?->format('M d, Y') ?? 'Flexible start' }}</p>
            <h5 class="mb-0">{{ $course->title }}</h5>
          </div>
        </div>
        <p class="text-muted">{{ Str::limit($course->description, 120) }}</p>
        <div class="d-flex justify-content-between align-items-center">
          <span class="fw-semibold">৳{{ number_format($course->price, 2) }}</span>
          <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary">View</a>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</section>
@endif

@if($featuredTeachers->isNotEmpty())
<section class="mb-5">
  <h3 class="section-heading">Featured mentors</h3>
  <div class="row g-4">
    @foreach($featuredTeachers as $teacher)
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="d-flex align-items-center gap-3 mb-3">
          @if($teacher->profile_picture_url)
          <img src="{{ $teacher->profile_picture_url }}" alt="{{ $teacher->name }}" class="rounded-3" width="70" height="70" style="object-fit:cover;">
          @else
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:70px;height:70px;background:#ede9fe;font-weight:700;font-size:1.5rem;">
            {{ strtoupper(substr($teacher->name,0,1)) }}
          </div>
          @endif
          <div>
            <h5 class="mb-0">{{ $teacher->name }}</h5>
            <small class="text-muted">{{ $teacher->specialization ?? 'Education Expert' }}</small>
          </div>
        </div>
        <p class="text-muted">{{ Str::limit($teacher->bio ?? 'This mentor crafts immersive learning paths across disciplines.', 120) }}</p>
        <div class="teacher-pill mb-3">{{ $teacher->teaching_courses_count }} courses</div>
        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-primary w-100">Meet {{ $teacher->name }}</a>
      </div>
    </div>
    @endforeach
  </div>
</section>
@endif

<section class="row g-4 mb-5">
  <div class="col-md-6">
    <div class="community-card h-100">
      <h4>Community-first academy OS</h4>
      <p class="text-muted">From trigger-based emails to live course announcements, every interaction keeps your brand
        voice intact. Automations stay thoughtful, never spammy.</p>
      <ul class="list-unstyled mb-0">
        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Customizable notice boards</li>
        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Stripe-powered teacher payouts</li>
        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Performance dashboards for every role</li>
      </ul>
    </div>
  </div>
  <div class="col-md-6">
    <div class="community-card h-100">
      <h4>Trusted by ambitious teams</h4>
      <p class="text-muted">Academics, bootcamps, and creative studios rely on our analytics layer to keep growth and
        wellbeing in balance.</p>
      <div class="d-flex flex-wrap gap-3">
        <div class="course-pill"><i class="bi bi-building me-1"></i>Campus HQ</div>
        <div class="course-pill"><i class="bi bi-braces-asterisk me-1"></i>Coding Guild</div>
        <div class="course-pill"><i class="bi bi-palette2 me-1"></i>Design Studios</div>
        <div class="course-pill"><i class="bi bi-mic me-1"></i>Creator Labs</div>
      </div>
    </div>
  </div>
</section>

<section class="cta-panel text-center">
  <p class="text-uppercase mb-2 fw-semibold" style="letter-spacing:0.2em;">Ready to elevate your academy?</p>
  <h2 class="mb-4">Create cinematic learning moments with the tools your team deserves.</h2>
  <div class="d-flex flex-wrap justify-content-center gap-3">
    <a href="{{ route('courses.index') }}" class="btn btn-light text-primary">
      Browse programs
    </a>
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-light">
      Meet our mentors
    </a>
  </div>
</section>
@endsection
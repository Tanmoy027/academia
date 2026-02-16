@extends('layouts.bootstrap')

@section('title', 'Welcome - Academy System')

@section('content')
<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Academy Management System</h1>
    <p class="col-md-8 fs-4">Analyze and manage your educational institution with ease. Join us to experience the best learning management environment.</p>
    @auth
    <a class="btn btn-primary btn-lg" href="{{ url('/dashboard') }}">Go to Dashboard</a>
    @else
    <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Get Started</a>
    <a class="btn btn-outline-secondary btn-lg" href="{{ route('login') }}">Login</a>
    @endauth
    <a class="btn btn-info btn-lg ms-2" href="{{ route('courses.index') }}">View All Courses</a>
  </div>
</div>

<div class="row align-items-md-stretch">
  <div class="col-md-6">
    <div class="h-100 p-5 text-bg-dark rounded-3">
      <h2>For Teachers</h2>
      <p>Manage your classes, grade students, and share resources efficiently.</p>
      <button class="btn btn-outline-light" type="button">Learn More</button>
    </div>
  </div>
  <div class="col-md-6">
    <div class="h-100 p-5 bg-body-tertiary border rounded-3">
      <h2>For Students</h2>
      <p>Access your courses, view grades, and communicate with instructors seamlessly.</p>
      <button class="btn btn-outline-secondary" type="button">Learn More</button>
    </div>
  </div>
</div>
@endsection
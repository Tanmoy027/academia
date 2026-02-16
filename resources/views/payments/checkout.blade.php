@extends('layouts.bootstrap')

@section('title', 'Checkout - ' . $course->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none"><i class="bi bi-house me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course->id) }}" class="text-decoration-none">{{ Str::limit($course->title, 20) }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                <!-- Header -->
                <div class="card-header py-4 text-white text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-shield-lock display-4 mb-3"></i>
                    <h3 class="fw-bold mb-1">Secure Checkout</h3>
                    <p class="mb-0 opacity-75">Complete your enrollment with Stripe</p>
                </div>

                <div class="card-body p-5">
                    <!-- Course Summary -->
                    <div class="d-flex align-items-start mb-4 p-4 rounded-4" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);">
                        @if($course->image_url)
                        <img src="{{ $course->image_url }}" class="rounded-3 me-4" style="width: 100px; height: 80px; object-fit: cover;" alt="{{ $course->title }}" onerror="this.style.display='none';">
                        @else
                        <div class="rounded-3 me-4 d-flex align-items-center justify-content-center" style="width: 100px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-book text-white fs-3"></i>
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $course->title }}</h5>
                            <p class="text-muted mb-2 small">{{ Str::limit($course->description, 80) }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                @if($course->duration)
                                <span class="badge bg-primary"><i class="bi bi-clock me-1"></i>{{ $course->duration }}</span>
                                @endif
                                @if($course->teacher)
                                <span class="badge bg-secondary"><i class="bi bi-person me-1"></i>{{ $course->teacher->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-receipt me-2 text-primary"></i>Order Summary</h5>
                        <div class="border rounded-3 p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Course Price</span>
                                <span>৳{{ number_format($course->price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Discount</span>
                                <span class="text-success">৳0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-4" style="color: #667eea;">৳{{ number_format($course->price, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-circle me-2 text-primary"></i>Student Information</h5>
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Name</small>
                                    <p class="mb-0 fw-semibold">{{ Auth::user()->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Email</small>
                                    <p class="mb-0 fw-semibold">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <strong>Secure Payment via Stripe</strong><br>
                            <small>You'll be redirected to Stripe's secure checkout page to complete your payment.</small>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <form action="{{ route('payment.process', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-lg w-100 text-white fw-bold py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
                            <i class="bi bi-credit-card me-2"></i>Pay ৳{{ number_format($course->price, 2) }} with Stripe
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('courses.show', $course->id) }}" class="text-muted text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Cancel and go back
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer bg-light p-4 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
                        <img src="https://cdn.brandfolder.io/KGT2DTA4/at/8vbr8k4mr5xjwk4hxq4t9vs/Stripe_wordmark_-_blurple.svg" alt="Stripe" height="30">
                        <span class="text-muted">|</span>
                        <i class="bi bi-shield-check text-success fs-4"></i>
                        <span class="text-muted">|</span>
                        <i class="bi bi-lock-fill text-primary fs-4"></i>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-shield-lock me-1"></i>Your payment is secured with 256-bit SSL encryption
                    </p>
                </div>
            </div>

            <!-- What you get -->
            <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-gift me-2 text-success"></i>What you'll get</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Lifetime access to course</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Video lectures</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Study materials</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Certificate of completion</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
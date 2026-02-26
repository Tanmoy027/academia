@extends('layouts.bootstrap')

@section('title', 'About Academia')

@push('styles')
<style>
    .about-hero {
        background: linear-gradient(135deg, #0f172a, #1e1b4b);
        color: #fff;
        border-radius: 32px;
        padding: 3.5rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 30px 70px rgba(15, 23, 42, 0.35);
    }

    .about-hero h1 {
        font-size: clamp(2.4rem, 4vw, 3.2rem);
        font-weight: 700;
    }

    .value-card {
        border-radius: 22px;
        background: #fff;
        padding: 1.75rem;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
        height: 100%;
    }

    .value-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(99, 102, 241, 0.15);
        color: #4338ca;
        font-size: 1.5rem;
    }

    .timeline {
        border-left: 3px solid rgba(99, 102, 241, 0.2);
        padding-left: 1.5rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.75rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.65rem;
        top: 0.4rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #a855f7);
        box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.18);
    }

    .culture-card {
        border-radius: 26px;
        background: linear-gradient(135deg, #ec4899, #f97316);
        color: #fff;
        padding: 2.5rem;
        box-shadow: 0 35px 65px rgba(236, 72, 153, 0.3);
    }

    .culture-card .btn {
        border-radius: 14px;
        padding: 0.85rem 2rem;
        font-weight: 600;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }

    .team-card {
        border-radius: 22px;
        padding: 1.5rem;
        background: #fff;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
    }
</style>
@endpush

@section('content')
<section class="about-hero">
    <p class="text-uppercase fw-semibold text-white-50 mb-2">Our story</p>
    <h1>We build cinematic learning experiences for bold schools.</h1>
    <p class="fs-5 mb-0" style="color: rgba(255,255,255,0.78);">Academia started as a collective of facilitators frustrated with clunky LMS tools. Today we power live cohorts, hybrid universities, and boutique studios that put creativity first.</p>
</section>

<section class="mb-5">
    <h2 class="mb-4">Milestones</h2>
    <div class="timeline">
        @foreach($milestones as $milestone)
        <div class="timeline-item">
            <p class="text-uppercase text-muted fw-semibold mb-1">{{ $milestone['year'] }}</p>
            <h5 class="mb-1">{{ $milestone['title'] }}</h5>
            <p class="text-muted mb-0">{{ $milestone['detail'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<section class="mb-5">
    <h2 class="mb-4">Values we live by</h2>
    <div class="row g-4">
        @foreach($values as $value)
        <div class="col-md-4">
            <div class="value-card">
                <div class="value-icon mb-3"><i class="bi {{ $value['icon'] }}"></i></div>
                <h5>{{ $value['title'] }}</h5>
                <p class="text-muted mb-0">{{ $value['detail'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

<section class="mb-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="value-card h-100">
                <h4>People first, then product</h4>
                <p class="text-muted">We co-design with educators weekly. Every feature—performance weighting, salary payouts, or attendance automations—was storyboarded with real classrooms.</p>
                <div class="team-grid mt-3">
                    <div class="team-card">
                        <h6 class="mb-1">Facilitator Council</h6>
                        <p class="text-muted mb-0">12 cross-disciplinary teachers pressure-test roadmap items.</p>
                    </div>
                    <div class="team-card">
                        <h6 class="mb-1">Student Guild</h6>
                        <p class="text-muted mb-0">Weekly interviews ensure autonomy features actually help learners.</p>
                    </div>
                    <div class="team-card">
                        <h6 class="mb-1">Ops Pod</h6>
                        <p class="text-muted mb-0">Finance + enrollment pros keep compliance and payouts simple.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="culture-card h-100">
                <h4 class="mb-3">Join the movement</h4>
                <p class="mb-4">Whether you are designing a micro-school or scaling a remote bootcamp, Academia serves as your learning operating system. Bring your brand, we bring the infrastructure.</p>
                <a href="{{ route('register') }}" class="btn btn-light text-danger">
                    Become a partner
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

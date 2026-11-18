@extends('layouts.app')

@section('title', $roleName . ' Dashboard - Baseline IT Development')

@section('content')
<div class="py-5" style="background: linear-gradient(180deg, #F8FAFC 0%, #EEF2F6 100%); min-height: 80vh;">
    <div class="container py-4">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Welcome Header Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-4 p-lg-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); color: #fff;">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                            <i class="bi {{ $roleIcon }}" style="color: {{ $roleColor }};"></i>
                            <span class="fs-12 fw-semibold text-uppercase tracking-wider">{{ $roleBadge }}</span>
                        </div>
                        <h2 class="fw-bold mb-2 text-white">Welcome, {{ $user->name }}!</h2>
                        <p class="text-white-50 mb-0 fs-15">
                            You are authenticated as <strong>{{ $roleName }}</strong> ({{ $user->designation ?? 'Team Member' }}). Your secure session is active.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-white px-4 py-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Information & Quick Summary --}}
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: rgba(37,99,235,0.08); color: #2563EB; font-size: 24px;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-navy fs-16">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                    <hr class="my-2 border-light">
                    <div class="d-flex justify-content-between py-2 fs-13">
                        <span class="text-muted">Designation:</span>
                        <span class="fw-semibold text-navy">{{ $user->designation ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 fs-13">
                        <span class="text-muted">Role Key:</span>
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(37,99,235,0.1); color: #2563EB;">{{ $user->role }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 fs-13">
                        <span class="text-muted">Account Status:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 fs-13">
                        <span class="text-muted">Joining Date:</span>
                        <span class="fw-medium text-navy">{{ $user->joining_date ? $user->joining_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 p-lg-5 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(13,148,136,0.08); color: #0D9488; font-size: 22px;">
                            <i class="bi bi-check2-all"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-navy mb-0">Phase 1 Authentication Verified</h4>
                            <small class="text-muted">TaskFlow Role-Based Security Engine</small>
                        </div>
                    </div>

                    <p class="text-muted fs-14 leading-relaxed mb-4">
                        Login authentication, password hashing, session regeneration, and role redirects are successfully active. The full dedicated {{ $roleName }} dashboard (analytics widgets, project boards, and sprint management tables) is staged for Phase 3.
                    </p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 border" style="background: #F8FAFC;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-shield-check text-success"></i>
                                    <span class="fw-semibold text-navy fs-13">Session Secured</span>
                                </div>
                                <small class="text-muted fs-12">Protected against CSRF, fixation &amp; brute-force</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 border" style="background: #F8FAFC;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-hdd-network text-primary"></i>
                                    <span class="fw-semibold text-navy fs-13">Database Connected</span>
                                </div>
                                <small class="text-muted fs-12">MySQL `taskflow_db` on localhost</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

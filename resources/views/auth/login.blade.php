<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In &bull; Baseline IT Development Workspace</title>

    {{-- Google Poppins Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Global Stylesheet --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    {{-- Dedicated Auth Stylesheet --}}
    <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="row g-0">

            {{-- Left Side: Corporate Blue Experience --}}
            <div class="col-lg-5 auth-brand-side">
                <div>
                    {{-- Logo & Status Badge --}}
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                            <span class="brand-logo-badge">
                                <i class="bi bi-layers-half"></i>
                            </span>
                            <div class="d-flex flex-column lh-1">
                                <span class="fw-bold text-white fs-18" style="letter-spacing: -0.3px;">Baseline <span style="color: #93C5FD;">IT</span></span>
                                <small class="text-white-50" style="font-size: 9px; letter-spacing: 0.7px;">DEVELOPMENT PVT LTD</small>
                            </div>
                        </a>
                        <div class="brand-badge">
                            <span class="status-indicator"></span>
                            <span>SSO / Active</span>
                        </div>
                    </div>

                    <h3 class="brand-heading">
                        Internal Project &amp; Taskflow Engine
                    </h3>
                    <p class="brand-subtitle">
                        Authorized enterprise access portal for software engineers, project managers, and administration.
                    </p>

                    {{-- Feature Cards with Translucent Glass Styling on Blue --}}
                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="auth-feature-pill d-flex align-items-center gap-3">
                            <div class="feature-icon-box">
                                <i class="bi bi-shield-lock-fill" style="color: #93C5FD;"></i>
                            </div>
                            <div>
                                <div class="text-white fw-semibold fs-13">Role-Isolated Boundaries</div>
                                <div class="fs-11" style="color: #DBEAFE;">Strict data permissions across 4 hierarchy tiers</div>
                            </div>
                        </div>

                        <div class="auth-feature-pill d-flex align-items-center gap-3">
                            <div class="feature-icon-box">
                                <i class="bi bi-diagram-3-fill" style="color: #6EE7B7;"></i>
                            </div>
                            <div>
                                <div class="text-white fw-semibold fs-13">Agile Sprint Delivery</div>
                                <div class="fs-11" style="color: #DBEAFE;">Real-time task tracking, review boards &amp; analytics</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Demo Credentials Pill Box (jQuery Driven Data Attributes) --}}
                <div class="demo-roles-wrap mt-auto">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-11 fw-semibold text-uppercase tracking-wider" style="color: #BFDBFE;">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Demo Autofill
                        </span>
                        <small style="color: #93C5FD; font-size: 11px;">Click role to test</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button"
                                class="demo-role-btn"
                                data-email="admin@baselineitdevelopment.com"
                                data-password="password"
                                data-role="Admin">
                            <i class="bi bi-shield-shaded" style="color: #FCA5A5;"></i> Admin
                        </button>

                        <button type="button"
                                class="demo-role-btn"
                                data-email="pm@baselineitdevelopment.com"
                                data-password="password"
                                data-role="Project Manager">
                            <i class="bi bi-briefcase-fill" style="color: #93C5FD;"></i> PM
                        </button>

                        <button type="button"
                                class="demo-role-btn"
                                data-email="tl@baselineitdevelopment.com"
                                data-password="password"
                                data-role="Team Leader">
                            <i class="bi bi-diagram-3-fill" style="color: #5EEAD4;"></i> Team Lead
                        </button>

                        <button type="button"
                                class="demo-role-btn"
                                data-email="employee@baselineitdevelopment.com"
                                data-password="password"
                                data-role="Software Engineer">
                            <i class="bi bi-person-badge-fill" style="color: #86EFAC;"></i> Developer
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Side: Login Form --}}
            <div class="col-lg-7 auth-form-side">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="fw-bold text-navy mb-1" style="font-size: 26px; letter-spacing: -0.5px;">Sign in to Workspace</h2>
                        <p class="text-muted fs-14 mb-0">Enter your Baseline IT credentials to continue</p>
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fs-12 text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Home
                    </a>
                </div>

                {{-- Status Alert Messages --}}
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 rounded-3 d-flex align-items-center gap-2 mb-4 fs-13 py-2 px-3" role="alert">
                        <i class="bi bi-info-circle-fill text-info fs-5"></i>
                        <span>{{ session('info') }}</span>
                        <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 d-flex align-items-center gap-2 mb-4 fs-13 py-2 px-3" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 d-flex align-items-start gap-2 mb-4 fs-13 py-2 px-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-1"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Autofill Notification Toast --}}
                <div id="autofillToast" class="alert alert-primary border-0 rounded-3 py-2 px-3 fs-13 d-none align-items-center gap-2 mb-3" style="background: rgba(37,99,235,0.08); color: var(--auth-brand-primary);">
                    <i class="bi bi-check2-circle fs-5"></i>
                    <span>Autofilled credentials for: <strong id="autofillRoleName">Admin</strong></span>
                </div>

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf

                    {{-- Email Input --}}
                    <div class="form-floating-custom">
                        <label for="email" class="form-label fs-13 fw-semibold text-navy mb-1">Corporate Email Address</label>
                        <div class="position-relative">
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', 'admin@baselineitdevelopment.com') }}"
                                   placeholder="name@baselineitdevelopment.com"
                                   autocomplete="email"
                                   required
                                   autofocus>
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>

                    {{-- Password Input --}}
                    <div class="form-floating-custom mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="password" class="form-label fs-13 fw-semibold text-navy mb-0">Account Password</label>
                            <a href="mailto:support@baselineitdevelopment.com?subject=Password%20Reset%20Request" class="fs-12 text-primary text-decoration-none">
                                Forgot password?
                            </a>
                        </div>
                        <div class="position-relative">
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   value="password"
                                   placeholder="••••••••"
                                   autocomplete="current-password"
                                   required>
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                            <label class="form-check-label fs-13 text-muted" for="remember">
                                Keep me signed in for 30 days
                            </label>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn-auth-submit" id="submitBtn">
                        <span id="btnText">Sign In to Workspace</span>
                        <i class="bi bi-arrow-right fs-5" id="btnIcon"></i>
                        <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status" aria-hidden="true"></span>
                    </button>
                </form>

                {{-- Security Tagline --}}
                <div class="auth-footer-tag">
                    <i class="bi bi-shield-check text-success"></i>
                    <span>Protected by 256-bit Session Security &bull; Baseline IT Pvt Ltd</span>
                </div>
            </div>

        </div>
    </div>

    {{-- jQuery 3.7.1 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap 5 Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Dedicated Auth jQuery Script --}}
    <script src="{{ asset('assets/js/auth.js') }}"></script>
</body>
</html>

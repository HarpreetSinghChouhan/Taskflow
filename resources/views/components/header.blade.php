<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">

            {{-- Brand Logo --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-logo-icon">
                    <i class="bi bi-layers-half"></i>
                </span>
                <div class="d-flex flex-column lh-1">
                    <span class="brand-title">
                        Baseline <span class="brand-accent">IT</span>
                    </span>
                    <small class="text-muted fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">DEVELOPMENT PVT LTD</small>
                </div>
            </a>

            {{-- Mobile Toggler --}}
            <button class="navbar-toggler border-0 shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Navbar Links --}}
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @auth
                        <a href="{{ Auth::user()->getDashboardRoute() }}" class="btn btn-outline-brand">
                            <i class="bi bi-speedometer2"></i> Dashboard ({{ Auth::user()->name }})
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-brand" title="Logout">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-brand">
                            <i class="bi bi-box-arrow-in-right"></i> Employee Login
                        </a>
                        <a href="#cta" class="btn btn-brand">
                            Get Started <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </nav>
</header>

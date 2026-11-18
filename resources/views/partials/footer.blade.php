<footer class="site-footer">
    <div class="container">
        <div class="row g-4 footer-top">

            {{-- Brand info --}}
            <div class="col-lg-4 col-md-6 reveal">
                <a class="navbar-brand d-flex align-items-center gap-2 mb-3" href="{{ route('home') }}">
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
                <p class="footer-text mb-3">
                    <strong>Baseline IT Development Pvt Ltd</strong> &bull; Internal Project &amp; Task Management System designed to orchestrate software projects, sprint cycles, and cross-functional teams with maximum velocity.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-navy border px-2 py-1 fs-12"><i class="bi bi-cpu me-1"></i> Laravel 12</span>
                    <span class="badge bg-light text-navy border px-2 py-1 fs-12"><i class="bi bi-database me-1"></i> MySQL</span>
                    <span class="badge bg-light text-navy border px-2 py-1 fs-12"><i class="bi bi-shield-check me-1"></i> Role Protected</span>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6 col-6 reveal" style="transition-delay:.08s">
                <h4 class="footer-heading">Navigation</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#about">About Company</a></li>
                    <li><a href="#contact">Contact Desk</a></li>
                </ul>
            </div>

            {{-- Roles / Dashboards --}}
            <div class="col-lg-3 col-md-6 col-6 reveal" style="transition-delay:.16s">
                <h4 class="footer-heading">Workspaces</h4>
                <ul class="footer-links">
                    <li><a href="{{ url('/login') }}"><i class="bi bi-shield-lock me-1" style="color:#2563EB"></i> Admin Portal</a></li>
                    <li><a href="{{ url('/login') }}"><i class="bi bi-briefcase me-1" style="color:#2563EB"></i> Project Manager</a></li>
                    <li><a href="{{ url('/login') }}"><i class="bi bi-diagram-3 me-1" style="color:#0D9488"></i> Team Leader</a></li>
                    <li><a href="{{ url('/login') }}"><i class="bi bi-person me-1" style="color:#16A34A"></i> Developer Portal</a></li>
                </ul>
            </div>

            {{-- Contact / Environment --}}
            <div class="col-lg-3 col-md-6 reveal" style="transition-delay:.24s">
                <h4 class="footer-heading">Company Information</h4>
                <ul class="footer-links footer-contact">
                    <li><i class="bi bi-building me-2" style="color:#2563EB"></i> Baseline IT Development Pvt Ltd</li>
                    <li><i class="bi bi-envelope me-2" style="color:#2563EB"></i> support@baselineitdevelopment.com</li>
                    <li><i class="bi bi-hdd-network me-2" style="color:#2563EB"></i> Localhost / XAMPP Server</li>
                    <li><i class="bi bi-clock me-2" style="color:#2563EB"></i> Mon &ndash; Fri (9:30 AM &ndash; 6:30 PM)</li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div>
                &copy; {{ date('Y') }} <strong>Baseline IT Development Pvt Ltd</strong> &bull; All rights reserved.
            </div>
            <div class="d-flex gap-3 fs-13">
                <a href="#hero" class="text-muted text-decoration-none hover-primary">Back to Top <i class="bi bi-arrow-up"></i></a>
            </div>
        </div>
    </div>
</footer>

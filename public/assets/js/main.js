/**
 * main.js — Professional UI Interactions
 * Scroll-reveal (Intersection Observer), smooth scroll,
 * navbar scroll effect, scroll-to-top, page loader
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Page Loader ──
    const loader = document.querySelector('.page-loader');
    if (loader) {
        window.addEventListener('load', () => {
            loader.classList.add('loaded');
            setTimeout(() => loader.remove(), 600);
        });
        // Fallback: remove after 2s even if load doesn't fire
        setTimeout(() => {
            if (loader && !loader.classList.contains('loaded')) {
                loader.classList.add('loaded');
                setTimeout(() => loader.remove(), 600);
            }
        }, 2000);
    }

    // ── Navbar Scroll Effect ──
    const header = document.querySelector('.site-header');
    if (header) {
        const onScroll = () => {
            header.classList.toggle('scrolled', window.scrollY > 30);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ── Scroll-Reveal (Intersection Observer) ──
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
    if (revealElements.length && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        // Fallback: show everything
        revealElements.forEach(el => el.classList.add('revealed'));
    }

    // ── Scroll-to-Top Button ──
    const scrollBtn = document.querySelector('.scroll-top-btn');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('visible', window.scrollY > 500);
        }, { passive: true });

        scrollBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── Smooth anchor scrolling with offset ──
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const headerH = header ? header.offsetHeight : 0;
                const top = target.getBoundingClientRect().top + window.pageYOffset - headerH - 16;
                window.scrollTo({ top, behavior: 'smooth' });

                // Close mobile nav if open
                const navCollapse = document.querySelector('#mainNav');
                if (navCollapse && navCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            }
        });
    });

    // ── Active nav link on scroll ──
    const sections = document.querySelectorAll('section[id]');
    if (sections.length) {
        const navLinks = document.querySelectorAll('.site-header .nav-link');
        window.addEventListener('scroll', () => {
            const scrollPos = window.scrollY + 120;
            sections.forEach(section => {
                const top = section.offsetTop;
                const bottom = top + section.offsetHeight;
                const id = section.getAttribute('id');
                if (scrollPos >= top && scrollPos < bottom) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, { passive: true });
    }

    // ── Animated Counters (About stats) ──
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = el.getAttribute('data-count');
                    const suffix = el.getAttribute('data-suffix') || '';
                    const isNum = !isNaN(parseInt(target));
                    
                    if (isNum) {
                        animateCounter(el, 0, parseInt(target), 1200, suffix);
                    }
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.4 });

        counters.forEach(el => counterObserver.observe(el));
    }

    function animateCounter(el, start, end, duration, suffix) {
        const startTime = performance.now();
        const easeOut = t => 1 - Math.pow(1 - t, 3);
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const value = Math.round(start + (end - start) * easeOut(progress));
            el.textContent = value + suffix;
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        requestAnimationFrame(update);
    }

    // ── Tilt effect on dashboard preview (subtle parallax) ──
    const dashPreview = document.querySelector('.dashboard-preview-window');
    if (dashPreview && window.matchMedia('(min-width: 992px)').matches) {
        dashPreview.addEventListener('mousemove', (e) => {
            const rect = dashPreview.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            dashPreview.style.transform = `perspective(800px) rotateY(${x * 4}deg) rotateX(${-y * 4}deg) translateY(-4px)`;
        });

        dashPreview.addEventListener('mouseleave', () => {
            dashPreview.style.transform = 'perspective(800px) rotateY(0) rotateX(0) translateY(0)';
            dashPreview.style.transition = 'transform .5s cubic-bezier(.16,1,.3,1)';
        });

        dashPreview.addEventListener('mouseenter', () => {
            dashPreview.style.transition = 'transform .1s ease-out';
        });
    }

});

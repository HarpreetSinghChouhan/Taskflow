@php
    // Service list (later this can come from the database)
    $services = [
        [
            'title' => 'Web Development',
            'text'  => 'Responsive, user-centric websites that engage your audience and drive online success.',
            'icon'  => '<rect x="3" y="4" width="18" height="14" rx="2"/><path d="M3 9h18"/>',
        ],
        [
            'title' => 'App Development',
            'text'  => 'From concept to deployment, we turn ideas into intuitive mobile and web applications.',
            'icon'  => '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>',
        ],
        [
            'title' => 'UI/UX Design',
            'text'  => 'Visually appealing, easy-to-use interfaces that improve satisfaction and engagement.',
            'icon'  => '<path d="M4 20l4-1 11-11-3-3L5 16z"/><path d="M14 6l3 3"/>',
        ],
        [
            'title' => 'IoT & Embedded',
            'text'  => 'Connected devices and embedded systems that bring efficiency and insight to your operations.',
            'icon'  => '<circle cx="12" cy="12" r="3"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3"/>',
        ],
        [
            'title' => 'Cloud Services',
            'text'  => 'Scalable, flexible and secure cloud solutions that help your business grow.',
            'icon'  => '<path d="M7 18a4 4 0 0 1-.5-8A5.5 5.5 0 0 1 17 8.5 4 4 0 0 1 17 18z"/>',
        ],
        [
            'title' => 'DevOps',
            'text'  => 'Streamlined development and IT operations for faster, more reliable delivery.',
            'icon'  => '<path d="M4 12a8 8 0 0 1 14-5M20 12a8 8 0 0 1-14 5"/><path d="M18 3v4h-4M6 21v-4h4"/>',
        ],
    ];
@endphp

<section class="services" id="services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">
            A full range of services tailored to your business needs,
            from web and app development to IoT and cloud solutions.
        </p>

        <div class="row g-4">
            @foreach ($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card h-100">
                        <div class="service-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                 stroke="#0F66A8" stroke-width="1.6"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                {!! $service['icon'] !!}
                            </svg>
                        </div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['text'] }}</p>
                        <a href="#" class="read-more">Read more</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
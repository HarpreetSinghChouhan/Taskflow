@php
    // Portal roles
    $roles = [
        [
            'title' => 'For Clients',
            'text'  => 'Follow project progress and milestones, download files, raise support tickets, view invoices and message your project manager.',
        ],
        [
            'title' => 'For Developers',
            'text'  => 'See assigned projects and tasks, update status, upload work, log hours and submit daily reports in one place.',
        ],
        [
            'title' => 'For Admins',
            'text'  => 'Manage clients, projects, staff, invoices and tickets, and review company performance reports.',
        ],
    ];
@endphp

<section class="portal">
    <div class="container">
        <h2 class="section-title">One Portal for Your Whole Project</h2>
        <p class="section-subtitle">
            Clients, developers and admins each get their own dashboard
            to track work, share files and stay in touch.
        </p>

        <div class="row g-4">
            @foreach ($roles as $role)
                <div class="col-md-6 col-lg-4">
                    <div class="portal-card h-100">
                        <h3>{{ $role['title'] }}</h3>
                        <p>{{ $role['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>  
@props(['title' => 'Dashboard', 'user' => null])

@php
    $user = $user ?? Auth::user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} &bull; Baseline IT TaskFlow</title>

    {{-- Google Poppins Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Dashboard Dedicated Stylesheet --}}
    <link href="{{ asset('assets/css/dashboard.css') }}?v={{ time() }}" rel="stylesheet">

    {{ $styles ?? '' }}
</head>
<body class="dashboard-body">

    {{-- Mobile Sidebar Backdrop --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="dashboard-wrapper">

        {{-- ── Sidebar Component ── --}}
        <x-sidebar :user="$user" />

        {{-- ── Main Area ── --}}
        <div class="dash-main-area">

            {{-- ── Topnav Component ── --}}
            <x-topnav :title="$title" :user="$user" />

            {{-- ── Body Content ── --}}
            <main class="dash-content-body">
                {{-- Flash Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div class="fw-medium fs-14">{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                        <i class="bi bi-info-circle-fill text-info fs-5"></i>
                        <div class="fw-medium fs-14">{{ session('info') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <div class="fw-medium fs-14">{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{ $slot }}
            </main>

        </div>
    </div>

    {{-- jQuery 3.7.1 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap 5 Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Chart.js (for analytics) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    {{-- Dashboard Dedicated Script --}}
    <script src="{{ asset('assets/js/dashboard.js') }}?v={{ time() }}"></script>

    {{ $scripts ?? '' }}
</body>
</html>

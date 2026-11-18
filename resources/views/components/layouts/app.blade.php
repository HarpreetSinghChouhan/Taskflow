@props(['title' => 'Baseline IT Development Pvt Ltd - Project & Task Management System'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    {{-- Google Poppins Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Custom Stylesheet --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    {{ $styles ?? '' }}
</head>
<body>
    {{-- Page Loader --}}
    <div class="page-loader">
        <div class="loader-spinner"></div>
    </div>

    {{-- Modern Header Component --}}
    <x-header />

    <main>
        {{ $slot }}
    </main>

    {{-- Modern Footer Component --}}
    <x-footer />

    {{-- Scroll to Top --}}
    <button class="scroll-top-btn" aria-label="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    {{-- Bootstrap 5 JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    {{ $scripts ?? '' }}
</body>
</html>

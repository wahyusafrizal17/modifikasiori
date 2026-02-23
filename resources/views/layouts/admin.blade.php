<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .ts-wrapper { font-family: inherit; }
        .ts-wrapper.single .ts-control { border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; font-size: 0.875rem; height: 2.5rem; padding: 0 1rem; display: flex; align-items: center; box-shadow: none; cursor: pointer; }
        .ts-wrapper.single .ts-control::after { border-color: #9ca3af transparent transparent; margin-top: 1px; }
        .ts-wrapper.single .ts-control input { font-size: 0.875rem; }
        .ts-wrapper.single .ts-control .item { font-size: 0.875rem; color: #111827; }
        .ts-wrapper.single.focus .ts-control { border-color: #fca5a5; box-shadow: 0 0 0 2px rgba(239,68,68,0.1); }
        .ts-wrapper .ts-control > input::placeholder { color: #9ca3af; }
        .ts-dropdown { border: 1px solid #e5e7eb; border-radius: 0.75rem; margin-top: 4px; font-size: 0.875rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1); overflow: hidden; }
        .ts-dropdown .ts-dropdown-content { padding: 0.25rem 0; max-height: 220px; }
        .ts-dropdown .option { padding: 0.5rem 1rem; font-size: 0.875rem; color: #374151; cursor: pointer; }
        .ts-dropdown .option:hover, .ts-dropdown .option.active { background: #fef2f2; color: #dc2626; }
        .ts-dropdown .option.selected { background: #ef4444; color: #fff; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-100 antialiased" style="font-family: 'Inter', sans-serif;">
    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <div class="flex flex-1 flex-col">
            @include('partials.navbar')

            <main class="flex-1 p-8">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

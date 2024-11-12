<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-50">
        @auth
            <!-- Sidebar -->
            <div :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }" class="transition-all duration-300 ease-in-out">
                <x-sidebar />
            </div>

            <div class="flex-1 flex flex-col overflow-hidden">
                <x-header />
                <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                    @yield('content')
                </main>
                <x-footer />
            </div>
        @else
            <main class="flex-1">
                @yield('content')
            </main>
        @endauth
    </div>
</body>

</html>

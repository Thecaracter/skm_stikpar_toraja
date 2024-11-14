<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    @yield('content')
    @stack('scripts')
    <script>
        console.log(`
        ╔═══════════════════════════════════════════════╗
        ║     👨‍💻 Created with Love by:                 ║ 
        ║     🌟 Codinging.ind                         ║
        ║     💻 Rizqi Nur Andi Putra                  ║
        ║                                              ║
        ║     🎮 Butuh Joki Project/Tugas?             ║
        ║     💯 Dijamin Aman, Cepat & Berkualitas!    ║
        ║     💎 Harga Mahasiswa Friendly              ║
        ║     ⚡ Proses Express 1x24 Jam               ║
        ║                                              ║
        ║     📱 Langsung DM TikTok: @coding.in_        ║
        ║     ✨ Your Code is Our Priority!            ║
        ╚═══════════════════════════════════════════════╝`);
    </script>
</body>

</html>

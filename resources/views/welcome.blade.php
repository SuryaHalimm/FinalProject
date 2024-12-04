<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Destinasi Kota</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-600 text-white">
    <div class="relative min-h-screen flex flex-col items-center justify-between">
        <!-- Background Image -->
        <img id="background" class="absolute inset-0 w-full h-full object-cover opacity-40 pointer-events-none"
            src="{{ asset('storage/image/Bali.jpg') }}" alt="Travel Background" />
        <!-- Navbar -->
        <header class="relative z-10 w-full max-w-7xl px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
            </div>
            @if (Route::has('login'))
                <nav class="flex space-x-4">
                    @auth
                        @hasrole('admin')
                            <a href="{{ route('admin.dashboard.index') }}"
                                class="px-3 py-2 transition rounded-md hover:bg-white/20 hover:text-white">Dashboard</a>
                            <a href="{{ route('admin.report.index') }}"
                                class="px-3 py-2 transition rounded-md hover:bg-white/20 hover:text-white">Report</a>
                            <a href="{{ route('kota.view') }}"
                                class="px-3 py-2 transition rounded-md hover:bg-white/20 hover:text-white">Kota</a>
                        @endhasrole
                    @else
                        <a href="{{ route('login') }}"
                            class="px-3 py-2 transition rounded-md hover:bg-white/20 hover:text-white">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-3 py-2 transition rounded-md hover:bg-white/20 hover:text-white">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Main Section -->
        <main class="relative z-10 w-full max-w-7xl px-6 text-center py-20 space-y-12">
            <h1 class="text-5xl font-bold leading-tight">
                Temukan Kota Wisata Terbaik
            </h1>
            <p class="text-lg text-white/80">
                Jelajahi rekomendasi pilihan untuk petualangan Anda berikutnya, mulai dari kota yang ramai hingga
                permata tersembunyi di seluruh Indonesia.
            </p>
            <a href="{{ route('kota.view') }}"
                class="inline-block bg-white text-blue-600 px-6 py-3 rounded-full text-lg font-medium hover:bg-blue-100 transition">
                Telusuri Kota
            </a>
        </main>

        <!-- Featured Cities -->
        <section id="cities"
            class="relative z-10 w-full max-w-7xl px-6 py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Sample Card -->
            @if ($kota->isEmpty())
                <div class="w-full text-center">
                    <p class="text-lg text-gray-500">Data Kosong</p>
                </div>
            @else
                @foreach ($kota as $city)
                    <div class="bg-white/10 rounded-lg shadow-lg overflow-hidden">
                        <img src="{{ asset('storage/image/' . $city->nama_kota . '.jpg') }}"
                            alt="{{ $city->nama_kota }}" class="h-72 w-full object-cover object-top">
                        <div class="p-6">
                            <h3 class="text-2xl font-semibold">{{ $city->nama_kota }}</h3>
                            <p class="text-sm text-white/80 mt-2">{{ Str::limit($city->deskripsi, 150, '...') }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </section>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-7xl px-6 py-8 text-center text-sm text-white/70">
            ©2024 All rights reserved.
        </footer>
    </div>
</body>

</html>

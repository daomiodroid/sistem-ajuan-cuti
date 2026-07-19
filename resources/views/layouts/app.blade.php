<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title>Aplikasi Pengajuan Cuti Pegawai</title>
    <meta name="description" content="Aplikasi sederhana untuk mengajukan dan memproses data cuti pegawai.">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#d9e2ff',
                            200: '#bcccff',
                            300: '#93a7ff',
                            400: '#6479ff',
                            500: '#404eff',
                            600: '#2b2cff',
                            700: '#1e1bff',
                            800: '#1915e6',
                            900: '#1714bc',
                        },
                        darkBg: '#090d16',
                        glassBg: 'rgba(15, 23, 42, 0.65)',
                        glassBorder: 'rgba(255, 255, 255, 0.08)',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS Styles -->
    <style>
        body {
            background-color: #090d16;
            background-image: 
                radial-gradient(at 0% 0%, rgba(64, 78, 255, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
            color: #e2e8f0;
        }

        .glassmorphism {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s ease-in-out;
        }

        .glass-card:hover {
            border-color: rgba(64, 78, 255, 0.2);
            transform: translateY(-1px);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #090d16;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased">

    <!-- Navigation Bar -->
    <nav class="glassmorphism sticky top-0 z-50 px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-primary-500 w-9 h-9 rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-calendar-alt text-white text-base"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-white leading-none">Aplikasi Cuti</h1>
                    <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Tugas Pemrograman Web 3</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-white">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-300 px-3.5 py-2 rounded-xl bg-red-950/20 border border-red-900/30 transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-300 hover:text-white px-4 py-2 rounded-xl bg-slate-800/80 border border-slate-700 transition-all">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 pb-12">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-800/40 bg-slate-950/60 py-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p>&copy; 2026 Tugas UAS Pemrograman Web 3. Kelompok Mahasiswa.</p>
            <div class="flex gap-4 font-medium text-gray-600">
                <span>Laravel 11</span>
                <span>&bull;</span>
                <span>Supabase</span>
                <span>&bull;</span>
                <span>Tailwind CSS</span>
            </div>
        </div>
    </footer>

</body>
</html>

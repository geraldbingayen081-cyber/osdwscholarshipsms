<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'CSU–Lal-lo Scholarship Management System' }}</title>

    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased h-full text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 flex flex-col justify-between transition-colors duration-200">

    <!-- Top Institutional Banner -->
    <header class="bg-csu-green dark:bg-[#250308] border-b-4 border-csu-gold shadow-md text-white transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button type="button" onclick="toggleTheme()" class="cursor-pointer focus:outline-none rounded-full transition transform active:scale-95">
                    @if(\App\Models\SystemSetting::logoUrl())
                        <img src="{{ \App\Models\SystemSetting::logoUrl() }}" alt="Logo" class="h-14 w-14 rounded-full object-cover border-2 border-csu-gold shadow-md bg-white shrink-0">
                    @else
                        <x-csu-logo class="h-14 w-14 rounded-full shadow-md shrink-0 border-2 border-csu-gold" />
                    @endif
                </button>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white leading-tight uppercase">
                        {{ \App\Models\SystemSetting::get('institution_name', 'CAGAYAN STATE UNIVERSITY') }}
                    </h1>
                    <p class="text-xs sm:text-sm font-medium text-csu-gold">
                        {{ \App\Models\SystemSetting::get('campus_name', 'Lal-lo Campus') }} • {{ \App\Models\SystemSetting::get('office_name', 'Office of Student Development and Welfare (OSDW)') }}
                    </p>
                </div>
            </div>
            <div class="hidden md:block text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#3B060F] dark:bg-[#1b0205] text-csu-gold border border-csu-gold/30">
                    {{ \App\Models\SystemSetting::get('system_acronym', 'Scholarship Management Portal') }}
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-xl">
            <!-- Alert Notifications -->
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-init="setTimeout(() => show = false, 1500)" 
                     x-show="show" 
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 text-sm font-medium flex items-start space-x-3 shadow-sm">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-950/80 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 text-sm font-medium flex items-start space-x-3 shadow-sm">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-[#3B060F] dark:bg-[#1a0205] text-rose-200/80 border-t border-[#480913] dark:border-[#33040a] text-xs py-6 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 text-center space-y-2">
            <p class="font-bold text-[#FFC107]">
                &copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('institution_name', 'Cagayan State University') }} – {{ \App\Models\SystemSetting::get('campus_name', 'Lal-lo Campus') }}. All Rights Reserved.
            </p>
            <p class="text-rose-200/70 dark:text-rose-300/60 font-medium">
                {{ \App\Models\SystemSetting::get('system_name', 'Official Scholarship Management Portal') }} • {{ \App\Models\SystemSetting::get('office_name', 'Office of Student Development and Welfare (OSDW)') }}
            </p>
        </div>
    </footer>

    <script>
        // Global Theme Toggle Function
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }

        // Clear any client auth tokens/state on guest pages while preserving theme preference
        try {
            const savedTheme = localStorage.getItem('color-theme');
            localStorage.clear();
            sessionStorage.clear();
            if (savedTheme) {
                localStorage.setItem('color-theme', savedTheme);
            }
        } catch(e) {}

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>
</html>

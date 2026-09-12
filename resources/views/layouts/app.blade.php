<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC] dark:bg-slate-950">
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
    
    <!-- Alpine.js for lightweight mobile navigation & dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased h-full text-slate-800 dark:text-slate-100 flex flex-col min-h-screen bg-[#F8FAFC] dark:bg-slate-950 transition-colors duration-200" x-data="{ sidebarOpen: false, logoutModalOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-30 lg:hidden" 
             style="display: none;"></div>

        <!-- Sidebar Navigation (Responsive: Collapsible Drawer on Mobile, Fixed Sidebar on Desktop) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-40 w-64 bg-[#3B060F] dark:bg-[#1a0205] text-white flex flex-col shrink-0 border-r border-[#480913] dark:border-[#30030a] shadow-2xl transition-all duration-300 ease-in-out lg:static lg:z-20 lg:shadow-xl">
            
            <!-- Brand Header with Official Seal & Close Button for Mobile -->
            <div class="px-5 py-4 flex items-center justify-between border-b border-[#480913] dark:border-[#30030a]">
                <div class="flex items-center space-x-3">
                    <!-- Hidden Dark Mode Toggle on Sidebar Logo Click -->
                    <button type="button" onclick="toggleTheme()" class="cursor-pointer focus:outline-none rounded-full transition transform active:scale-95 shrink-0">
                        @if(\App\Models\SystemSetting::logoUrl())
                            <img src="{{ \App\Models\SystemSetting::logoUrl() }}" alt="Logo" class="h-10 w-10 rounded-full object-cover shrink-0 border border-white/20 shadow-xs bg-white">
                        @else
                            <x-csu-logo class="h-10 w-10 rounded-full shrink-0 shadow-xs" />
                        @endif
                    </button>
                    <div class="overflow-hidden">
                        <h2 class="font-extrabold text-xs tracking-wider text-white leading-tight uppercase truncate max-w-[140px]" title="{{ \App\Models\SystemSetting::get('institution_name', 'CAGAYAN STATE UNIVERSITY') }}">
                            {{ \App\Models\SystemSetting::get('institution_name', 'CAGAYAN STATE UNIVERSITY') }}
                        </h2>
                        <p class="text-[10px] font-bold text-[#FFC107] tracking-widest mt-0.5 uppercase truncate max-w-[140px]" title="{{ \App\Models\SystemSetting::get('campus_name', 'LAL-LO CAMPUS') }}">
                            {{ \App\Models\SystemSetting::get('campus_name', 'LAL-LO CAMPUS') }}
                        </p>
                    </div>
                </div>

                <!-- Close Button (Mobile Only) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-300 hover:text-white p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                @if(Auth::user()->isAdmin())
                    <!-- ADMIN SIDEBAR MENU -->
                    <div class="px-3 pb-2 text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">
                        Admin Portal
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.dashboard', 'admin.dashboard.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.scholarships.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.scholarships.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        </svg>
                        Scholarships
                    </a>

                    <a href="{{ route('admin.applications.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.applications.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Applications
                    </a>

                    <a href="{{ route('admin.scholars.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.scholars.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Scholar Grantees
                    </a>

                    <a href="{{ route('admin.compliance.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.compliance.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Compliance Requests
                    </a>

                    <a href="{{ route('admin.students.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.students.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Registered Students
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports & Analytics
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        System Settings
                    </a>
                @else
                    <!-- STUDENT SIDEBAR MENU -->
                    <div class="px-3 pb-2 text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">
                        Student Portal
                    </div>

                    <a href="{{ route('student.dashboard') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('student.dashboard', 'student.dashboard.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('student.profile.show') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('student.profile.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Profile
                    </a>

                    <a href="{{ route('student.scholarships.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('student.scholarships.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        </svg>
                        Available Scholarships
                    </a>

                    <a href="{{ route('student.applications.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('student.applications.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        My Applications
                    </a>

                    <a href="{{ route('student.compliance.index') }}" class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('student.compliance.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Compliance Requests
                    </a>

                    @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                    <a href="{{ route('student.notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 text-xs font-bold rounded-xl transition {{ request()->routeIs('student.notifications.*') ? 'bg-[#FFC107] text-[#3B060F] font-extrabold shadow-xs' : 'text-slate-200 hover:bg-[#500A15] hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notifications
                        </div>
                        @if($unreadCount > 0)
                            <span class="h-4 w-4 bg-red-600 text-white rounded-full text-[10px] flex items-center justify-center font-bold">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @endif
            </nav>

            <!-- Bottom User Logout Section -->
            <div class="p-4 border-t border-[#480913] bg-[#042217]">
                <div class="flex items-center space-x-3 mb-2">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->full_name }}" class="h-8 w-8 rounded-full object-cover border border-[#FFC107]/60 shadow-xs shrink-0">
                    @else
                        <div class="h-8 w-8 rounded-full bg-[#6B0F1A] text-[#FFC107] font-bold flex items-center justify-center border border-[#FFC107]/40 text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->full_name }}</p>
                        <p class="text-[10px] text-slate-300 capitalize truncate">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                <button type="button" 
                        @click="logoutModalOpen = true" 
                        class="w-full text-left px-2.5 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-950/40 rounded-lg transition flex items-center cursor-pointer">
                    <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sign Out
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3 flex items-center justify-between shadow-xs z-10 transition-colors duration-200">
                
                <div class="flex items-center space-x-3">
                    <!-- Mobile Hamburger Button (Visible on mobile/tablet screens < lg) -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white focus:outline-none p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Campus Title -->
                    <div class="flex items-center space-x-2">
                        <span class="text-[11px] sm:text-xs font-extrabold text-[#3B060F] dark:text-[#FFC107] uppercase tracking-wider truncate">
                            {{ \App\Models\SystemSetting::get('system_name', 'OSDW Scholarship Management System') }}
                        </span>
                    </div>
                </div>

                <!-- Right: Notifications & User Badge -->
                <div class="flex items-center space-x-3 sm:space-x-5">
                    @php $headerUnreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.applications.index') : route('student.notifications.index') }}" class="relative text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($headerUnreadCount > 0)
                            <span class="absolute -top-1 -right-1.5 h-4 w-4 bg-red-600 text-white rounded-full text-[10px] flex items-center justify-center font-bold">{{ $headerUnreadCount }}</span>
                        @endif
                    </a>

                    <a href="{{ Auth::user()->isAdmin() ? route('admin.settings.index') : route('student.profile.show') }}" class="flex items-center space-x-2 sm:space-x-3 pl-2 sm:pl-3 border-l border-slate-200 dark:border-slate-700 group hover:opacity-90 transition">
                        @if(Auth::user()->profile_photo_url)
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->full_name }}" class="h-8 w-8 rounded-full object-cover border border-[#6B0F1A] dark:border-[#FFC107] shadow-xs shrink-0">
                        @else
                            <div class="h-8 w-8 rounded-full bg-[#6B0F1A] text-white font-extrabold flex items-center justify-center text-xs shadow-xs shrink-0">
                                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight group-hover:text-[#6B0F1A] dark:group-hover:text-amber-400 transition">{{ Auth::user()->full_name }}</p>
                            <p class="text-[10px] font-semibold text-slate-400 capitalize">{{ Auth::user()->role }}</p>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-[#F8FAFC] dark:bg-slate-950 transition-colors duration-200">
                <!-- Notifications Flash -->
                @if(session('success'))
                    <div x-data="{ show: true }" 
                         x-init="setTimeout(() => show = false, 1500)" 
                         x-show="show" 
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-start space-x-3 shadow-xs">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/80 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 text-xs font-semibold flex items-start space-x-3 shadow-xs">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <!-- Clean Institutional Footer -->
            <footer class="bg-[#3B060F] dark:bg-[#1a0205] border-t border-[#480913] dark:border-[#33040a] text-[11px] text-rose-200/80 py-3 px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-1 shrink-0 transition-colors duration-200">
                <div class="text-center sm:text-left">
                    {{ \App\Models\SystemSetting::get('institution_name', 'Cagayan State University') }} - {{ \App\Models\SystemSetting::get('campus_name', 'Lal-lo Campus') }} | <span class="font-bold text-[#FFC107]">{{ \App\Models\SystemSetting::get('system_name', 'Scholarship Management System') }}</span>
                </div>
                <div class="font-semibold text-rose-200">
                    {{ Auth::user()->isAdmin() ? 'Admin Portal v1.0' : 'Student Portal v1.0' }}
                </div>
            </footer>

        </div>

    </div>

    <!-- Hidden Auto-Logout Form for Back-Button Navigation -->
    <form id="global-back-nav-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

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

        (function() {
            // Push history state so Back button triggers popstate from Dashboard/Protected pages
            if (window.history && window.history.pushState) {
                window.history.pushState({ page: 'protected_dashboard' }, document.title, window.location.href);
                
                window.addEventListener('popstate', function(event) {
                    // Clear client storage while preserving theme preference
                    try {
                        const savedTheme = localStorage.getItem('color-theme');
                        localStorage.clear();
                        sessionStorage.clear();
                        if (savedTheme) {
                            localStorage.setItem('color-theme', savedTheme);
                        }
                    } catch(e) {}

                    // Trigger automatic logout session clearing
                    const logoutForm = document.getElementById('global-back-nav-logout-form');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        window.location.replace("{{ route('logout') }}");
                    }
                });
            }

            // BFCache re-validation
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        })();
    </script>

    <!-- Logout Confirmation Modal -->
    <div x-show="logoutModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="logout-modal-title" 
         role="dialog" 
         aria-modal="true"
         @keydown.escape.window="logoutModalOpen = false">
        
        <!-- Backdrop with Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
             @click="logoutModalOpen = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="logoutModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 px-5 pt-6 pb-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:p-6 border border-slate-200 dark:border-slate-800">
                
                <div class="flex items-start space-x-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/80 border border-red-200 dark:border-red-800/60 text-red-600 dark:text-red-400 shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white" id="logout-modal-title">
                            Do you want to logout?
                        </h3>
                        <p class="mt-1.5 text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Are you sure you want to end your current session? You will need to log in again to access your account.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                    <button type="button" 
                            @click="logoutModalOpen = false" 
                            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 transition cursor-pointer border border-slate-200 dark:border-slate-700">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto m-0">
                        @csrf
                        <button type="submit" 
                                class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Yes, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

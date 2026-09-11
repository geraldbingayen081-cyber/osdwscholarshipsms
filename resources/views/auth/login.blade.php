<x-guest-layout title="Login - CSU–Lal-lo Scholarship Management System">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-md dark:shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-200">
        <!-- Top Institutional Header -->
        <div class="bg-[#3B060F] dark:bg-[#250308] px-6 py-6 text-center border-b-4 border-csu-gold flex flex-col items-center relative transition-colors duration-200">
            <!-- Hidden Dark Mode Toggle on Logo Click -->
            <button 
                type="button" 
                id="theme-toggle-logo" 
                onclick="toggleTheme()" 
                class="mb-3 cursor-pointer focus:outline-none rounded-full transition transform active:scale-95"
            >
                @if(\App\Models\SystemSetting::logoUrl())
                    <img src="{{ \App\Models\SystemSetting::logoUrl() }}" alt="Logo" class="h-16 w-16 rounded-full object-cover border-2 border-csu-gold shadow-lg bg-white p-0.5">
                @else
                    <x-csu-logo class="h-16 w-16 rounded-full shadow-lg border-2 border-csu-gold" />
                @endif
            </button>

            <h2 class="text-xl font-bold text-white tracking-wide">OSDW-Scholarship Management System</h2>
            <p class="text-xs text-csu-gold mt-1">Sign in with your registered account credentials</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Student ID / Admin Email Address -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="login_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Student ID <span class="text-red-500">*</span>
                    </label>
                    
                </div>
                <div class="relative rounded-md shadow-xs">
                    <input 
                        type="text" 
                        name="login_id" 
                        id="login_id" 
                        value="{{ old('login_id', old('email')) }}" 
                        required 
                        autofocus 
                        class="w-full px-4 py-2.5 rounded-lg border @if($errors->has('login_id') || $errors->has('email')) border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#6B0F1A] focus:border-[#6B0F1A] text-sm transition font-medium"
                        placeholder="e.g. 26-32424"
                    >
                </div>
                @if($errors->has('login_id'))
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $errors->first('login_id') }}</p>
                @elseif($errors->has('email'))
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-md shadow-xs">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-lg border @error('password') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-csu-green focus:border-csu-green text-sm transition"
                        placeholder="••••••••"
                    >
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-csu-green focus:ring-csu-green"
                    >
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">Keep me signed in</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-bold rounded-lg border border-transparent bg-csu-green text-white hover:bg-csu-green-dark focus:outline-hidden focus:ring-2 focus:ring-csu-green focus:ring-offset-2 transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In to Portal
                </button>
            </div>

            <!-- Divider -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Are you a student looking for scholarships?
                </p>
                <a 
                    href="{{ route('register') }}" 
                    class="mt-2 inline-block text-xs font-bold text-csu-green dark:text-[#FFC107] hover:underline"
                >
                    Create a Student Account &rarr;
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>

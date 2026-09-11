<x-guest-layout title="Student Account Registration - CSU–Lal-lo">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-md dark:shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-200">
        <!-- Top Institutional Header -->
        <div class="bg-[#3B060F] dark:bg-[#250308] px-6 py-6 text-center border-b-4 border-csu-gold flex flex-col items-center relative transition-colors duration-200">
            <!-- Hidden Dark Mode Toggle on Logo Click -->
            <button 
                type="button" 
                onclick="toggleTheme()" 
                class="mb-3 cursor-pointer focus:outline-none rounded-full transition transform active:scale-95"
            >
                @if(\App\Models\SystemSetting::logoUrl())
                    <img src="{{ \App\Models\SystemSetting::logoUrl() }}" alt="Logo" class="h-16 w-16 rounded-full object-cover border-2 border-csu-gold shadow-lg bg-white p-0.5">
                @else
                    <x-csu-logo class="h-16 w-16 rounded-full shadow-lg border-2 border-csu-gold" />
                @endif
            </button>

            <h2 class="text-xl font-bold text-white tracking-wide">Student Account Registration</h2>
            <p class="text-xs text-csu-gold mt-1">Create your official student scholarship portal account</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Section 1: Account Information -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-csu-green dark:text-amber-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    1. Personal Account Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="first_name" 
                            id="first_name" 
                            value="{{ old('first_name') }}" 
                            required 
                            maxlength="20"
                            class="w-full px-3 py-2 rounded-lg border @error('first_name') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="e.g. Juan"
                        >
                        @error('first_name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="middle_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Middle Name <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="middle_name" 
                            id="middle_name" 
                            value="{{ old('middle_name') }}" 
                            maxlength="20"
                            class="w-full px-3 py-2 rounded-lg border @error('middle_name') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="e.g. Santos"
                        >
                        @error('middle_name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="last_name" 
                            id="last_name" 
                            value="{{ old('last_name') }}" 
                            required 
                            maxlength="20"
                            class="w-full px-3 py-2 rounded-lg border @error('last_name') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="e.g. Dela Cruz"
                        >
                        @error('last_name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email & Password Grid -->
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Email Address -->
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            required 
                            class="w-full px-3 py-2 rounded-lg border @error('email') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="e.g. juandelacruz@gmail.com"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            class="w-full px-3 py-2 rounded-lg border @error('password') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="At least 8 characters"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            required 
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="Re-enter password"
                        >
                    </div>
                </div>
            </div>

            <!-- Section 2: Student Academic Information -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-csu-green dark:text-amber-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    2. Campus Student Profile
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Student ID -->
                    <div>
                        <label for="student_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Student ID <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="student_number" 
                            id="student_number" 
                            value="{{ old('student_number') }}" 
                            required 
                            maxlength="20"
                            class="w-full px-3 py-2 rounded-lg border @error('student_number') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @endif bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                            placeholder="e.g. 26-32424"
                        >
                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Format: XX-XXXXX (e.g. 26-32424)</p>
                        @error('student_number')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Number (+63 Prefix Fixed) -->
                    <div>
                        <label for="contact_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Philippine Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <div class="flex rounded-lg shadow-xs overflow-hidden border @error('contact_number') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800">
                            <span class="inline-flex items-center px-3 bg-slate-100 dark:bg-slate-700 border-r border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs">
                                +63
                            </span>
                            <input 
                                type="text" 
                                name="contact_number" 
                                id="contact_number" 
                                value="{{ old('contact_number') }}" 
                                required 
                                maxlength="10"
                                pattern="9[0-9]{9}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                                class="w-full px-3 py-2 text-sm bg-transparent text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-csu-green focus:border-csu-green border-none"
                                placeholder="9123456789"
                            >
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Enter 10 digits starting with 9 (e.g. 9123456789)</p>
                        @error('contact_number')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Course -->
                    <div class="sm:col-span-2">
                        <label for="course" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Course / Degree Program <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="course" 
                            id="course" 
                            required 
                            class="w-full px-3 py-2 rounded-lg border @error('course') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                        >
                            <option value="">-- Select Course --</option>
                            @foreach($courses as $courseOption)
                                <option value="{{ $courseOption }}" {{ old('course') === $courseOption ? 'selected' : '' }}>
                                    {{ $courseOption }}
                                </option>
                            @endforeach
                        </select>
                        @error('course')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Year Level -->
                    <div class="sm:col-span-2">
                        <label for="year_level" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Year Level <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="year_level" 
                            id="year_level" 
                            required 
                            class="w-full px-3 py-2 rounded-lg border @error('year_level') border-red-500 bg-red-50 dark:bg-red-950/40 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                        >
                            <option value="">-- Select Year Level --</option>
                            @foreach($yearLevels as $yr)
                                <option value="{{ $yr }}" {{ old('year_level') === $yr ? 'selected' : '' }}>
                                    {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                        @error('year_level')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-bold rounded-lg border border-transparent bg-csu-green text-white hover:bg-csu-green-dark focus:outline-hidden focus:ring-2 focus:ring-csu-green focus:ring-offset-2 transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Register Account & Access Portal
                </button>
            </div>

            <!-- Divider -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Already have a student or admin account?
                </p>
                <a 
                    href="{{ route('login') }}" 
                    class="mt-2 inline-block text-xs font-bold text-csu-green dark:text-[#FFC107] hover:underline"
                >
                    &larr; Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>

<x-app-layout title="My Profile - CSU–Lal-lo">
    
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Student Profile & Security</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Manage your personal account, security credentials, and academic details.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.activity-logs.index') }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Activity Log
                </a>
                <a href="#security" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Change Password
                </a>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-full text-xs font-bold border border-emerald-300 dark:border-emerald-800">
                    Active Student Account
                </span>
            </div>
        </div>

        <!-- 1. Student Profile Details Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-6 sm:p-8">
            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Profile Picture Upload Section -->
                <div class="border-b border-slate-100 dark:border-slate-800 pb-6">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Profile Picture
                    </h3>

                    <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <!-- Current Photo / Initials Avatar Preview -->
                        <div class="relative shrink-0">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->full_name }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6B0F1A] dark:border-[#FFC107] shadow-sm">
                            @else
                                <div class="h-20 w-20 rounded-full bg-[#3B060F] text-[#FFC107] font-extrabold flex items-center justify-center text-xl border-2 border-[#FFC107]/40 shadow-sm">
                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Upload Control -->
                        <div class="flex-1 text-center sm:text-left">
                            <label for="profile_photo" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Upload New Profile Photo
                            </label>
                            <input type="file" 
                                   name="profile_photo" 
                                   id="profile_photo" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   class="text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#6B0F1A] file:text-white hover:file:bg-[#500A15] cursor-pointer">
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Allowed formats: JPG, JPEG, PNG, WEBP. Maximum size: 5MB.</p>
                            @error('profile_photo')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 2. Account Details -->
                <div class="border-b border-slate-100 dark:border-slate-800 pb-6">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Account Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="first_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required maxlength="20" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="middle_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $user->middle_name) }}" maxlength="20" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                            @error('middle_name')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required maxlength="20" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                     <div class="mt-4">
                        <label for="contact_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Philippine Mobile Number (+63)</label>
                        <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700">
                            <span class="px-3 py-2 bg-slate-100 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-400">+63</span>
                            <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', str_replace('+63', '', $student->contact_number)) }}" required maxlength="10" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none border-none">
                        </div>
                        @error('contact_number')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- 3. Student Academic Info -->
                <div>
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        </svg>
                        Academic Profile
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="student_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Student ID</label>
                            <input type="text" name="student_number" id="student_number" value="{{ old('student_number', $student->student_number) }}" required maxlength="20" placeholder="e.g. 26-32424" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Format: XX-XXXXX (e.g. 26-32424)</p>
                            @error('student_number')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                       
                        <div class="sm:col-span-2">
                            <label for="course" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Course / Degree Program</label>
                            <select name="course" id="course" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                                @foreach($courses as $c)
                                    <option value="{{ $c }}" {{ old('course', $student->course) === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('course')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="year_level" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Year Level</label>
                            <select name="year_level" id="year_level" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#3B060F] outline-none">
                                @foreach($yearLevels as $yr)
                                    <option value="{{ $yr }}" {{ old('year_level', $student->year_level) === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                            @error('year_level')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-right">
                    <button type="submit" class="px-6 py-2.5 bg-[#6B0F1A] hover:bg-[#500A15] text-white font-extrabold rounded-xl text-xs transition shadow-sm cursor-pointer">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Security & Change Password Card -->
        <div id="security" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-6 sm:p-8">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Security & Password Change
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Update your account password to ensure your student profile remains secure.
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-[11px] font-bold border border-slate-200 dark:border-slate-700">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Password Protected
                </span>
            </div>

            @if(session('password_success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center space-x-2.5">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('password_success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 text-xs font-bold cursor-pointer">
                        &times;
                    </button>
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <x-password-input 
                        name="current_password" 
                        id="current_password"
                        autocomplete="current-password"
                        placeholder="Enter your current account password" 
                        required
                        class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none"
                    />
                    @error('current_password')
                        <p class="mt-1 text-xs text-rose-500 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            New Password <span class="text-rose-500">*</span>
                        </label>
                        <x-password-input 
                            name="password" 
                            id="password"
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters" 
                            required
                            class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none"
                        />
                        @error('password')
                            <p class="mt-1 text-xs text-rose-500 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Confirm New Password <span class="text-rose-500">*</span>
                        </label>
                        <x-password-input 
                            name="password_confirmation" 
                            id="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Re-enter new password" 
                            required
                            class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none"
                        />
                        @error('password_confirmation')
                            <p class="mt-1 text-xs text-rose-500 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Password Guidance Note -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    <p class="font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Security Recommendations:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 ml-1">
                        <li>Password must be at least 8 characters long.</li>
                        <li>Include uppercase letters, numbers, and symbols to maximize security against unauthorized access.</li>
                    </ul>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-[#3B060F] hover:bg-[#500A15] text-white font-extrabold rounded-xl text-xs transition shadow-xs flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

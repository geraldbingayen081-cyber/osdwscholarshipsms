<x-app-layout title="My Profile - CSU–Lal-lo">
    
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Student Profile</h2>
                <p class="text-xs text-slate-500">Manage your personal account, profile picture, and academic details.</p>
            </div>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold border border-emerald-300">
                Active Student Account
            </span>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-8">
            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Profile Picture Upload Section -->
                <div class="border-b border-slate-100 pb-6">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-csu-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Profile Picture
                    </h3>

                    <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <!-- Current Photo / Initials Avatar Preview -->
                        <div class="relative shrink-0">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->full_name }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6B0F1A] shadow-sm">
                            @else
                                <div class="h-20 w-20 rounded-full bg-[#3B060F] text-[#FFC107] font-extrabold flex items-center justify-center text-xl border-2 border-[#FFC107]/40 shadow-sm">
                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Upload Control -->
                        <div class="flex-1 text-center sm:text-left">
                            <label for="profile_photo" class="block text-xs font-bold text-slate-700 mb-1">
                                Upload New Profile Photo
                            </label>
                            <input type="file" 
                                   name="profile_photo" 
                                   id="profile_photo" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#6B0F1A] file:text-white hover:file:bg-[#500A15] cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Allowed formats: JPG, JPEG, PNG, WEBP. Maximum size: 5MB.</p>
                            @error('profile_photo')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 2. Account Details -->
                <div class="border-b border-slate-100 pb-6">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-csu-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Account Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required maxlength="20" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="middle_name" class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $user->middle_name) }}" maxlength="20" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
                            @error('middle_name')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required maxlength="20" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                     <div class="mt-4">
                            <label for="contact_number" class="block text-xs font-semibold text-slate-700 mb-1">Philippine Mobile Number (+63)</label>
                            <div class="flex rounded-xl overflow-hidden border border-slate-300">
                                <span class="px-3 py-2 bg-slate-100 border-r border-slate-300 text-xs font-bold text-slate-600">+63</span>
                                <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', str_replace('+63', '', $student->contact_number)) }}" required maxlength="10" class="w-full px-3 py-2 text-xs focus:outline-none border-none">
                            </div>
                            @error('contact_number')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                </div>

                <!-- 3. Student Academic Info -->
                <div>
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-csu-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        </svg>
                        Academic Profile
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="student_number" class="block text-xs font-semibold text-slate-700 mb-1">Student ID</label>
                            <input type="text" name="student_number" id="student_number" value="{{ old('student_number', $student->student_number) }}" required maxlength="20" placeholder="e.g. 26-32424" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
                            <p class="text-[11px] text-slate-400 mt-1">Format: XX-XXXXX (e.g. 26-32424)</p>
                            @error('student_number')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                       
                        <div class="sm:col-span-2">
                            <label for="course" class="block text-xs font-semibold text-slate-700 mb-1">Course / Degree Program</label>
                            <select name="course" id="course" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
                                @foreach($courses as $c)
                                    <option value="{{ $c }}" {{ old('course', $student->course) === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('course')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="year_level" class="block text-xs font-semibold text-slate-700 mb-1">Year Level</label>
                            <select name="year_level" id="year_level" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-csu-green">
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

                <div class="pt-4 border-t border-slate-100 text-right">
                    <button type="submit" class="px-6 py-2.5 bg-[#6B0F1A] text-white font-extrabold rounded-xl text-xs hover:bg-[#500A15] transition shadow-sm">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

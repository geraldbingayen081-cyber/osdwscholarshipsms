<x-app-layout title="Student Profile - CSU Lal-lo Admin">

    <div x-data="{
        editModalOpen: {{ $errors->any() ? 'true' : 'false' }},
        college: '{{ old('college', $student->college?->value ?? '') }}',
        program: '{{ old('program', $student->program ?? $student->course ?? '') }}',
        programsByCollege: {
            'CAg': [
                'BS Agriculture (Crop Science)',
                'BS Agriculture (Animal Science)',
                'Diploma in Agricultural Technology - Bachelor in Agricultural Technology (DAT-BAT)'
            ],
            'CHM': [
                'BS Hospitality Management (BSHM)'
            ],
            'CICS': [
                'BS Information Technology (BSIT)'
            ],
            'CTE': [
                'Bachelor of Elementary Education (BEEd)',
                'Bachelor of Secondary Education - English (BSEd-ENG)',
                'Bachelor of Secondary Education - Mathematics (BSEd-MATH)',
                'Bachelor of Secondary Education - Science (BSEd-SCI)',
                'Bachelor of Secondary Education - Filipino (BSEd-FIL)',
                'Bachelor of Secondary Education - Social Studies (BSEd-SOC)'
            ]
        },
        get availablePrograms() {
            return this.programsByCollege[this.college] || [];
        }
    }">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('admin.students.index') }}" class="hover:text-[#6B0F1A] transition">&larr; Registered Students</a>
                    <span>/</span>
                    <span class="text-slate-800">Student Profile</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    {{ $student->user->full_name }}
                </h1>
            </div>

            <!-- Edit Button Trigger -->
            <div class="flex items-center gap-3">
                <button type="button" 
                        @click="editModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#3B060F] hover:bg-[#6B0F1A] text-white font-extrabold text-xs rounded-xl shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile & Password
                </button>
            </div>
        </div>

        <!-- Student Detail Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Profile Info & Applications History -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Personal Info Card -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                        <div class="flex items-center space-x-4">
                            @if($student->user->profile_photo_url)
                                <img src="{{ $student->user->profile_photo_url }}" alt="{{ $student->user->full_name }}" class="h-14 w-14 rounded-full object-cover border-2 border-[#6B0F1A] shadow-xs shrink-0">
                            @else
                                <div class="h-12 w-12 rounded-full bg-[#3B060F] text-[#FFC107] font-extrabold flex items-center justify-center text-base border border-[#FFC107]/40 shadow-xs shrink-0">
                                    {{ strtoupper(substr($student->user->first_name, 0, 1) . substr($student->user->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">
                                    Student Profile Overview
                                </h3>
                                <p class="text-xs text-slate-500 font-mono">{{ $student->student_number }}</p>
                            </div>
                        </div>

                        <button type="button" 
                                @click="editModalOpen = true"
                                class="text-xs font-bold text-[#6B0F1A] hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Full Name</span>
                            <span class="font-extrabold text-slate-900 text-sm mt-0.5 block">{{ $student->user->full_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Student ID Number</span>
                            <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $student->student_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">College</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->college_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Degree Program / Course</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->program_display }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Year Level</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->year_level ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Email Address</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->user->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Contact Number</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->contact_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Current GWA</span>
                            <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $student->current_gwa ? number_format($student->current_gwa, 2) : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Monthly Household Income</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->monthly_household_income ? '₱' . number_format($student->monthly_household_income, 2) : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">4Ps Beneficiary</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->is_4ps ? 'Yes' : 'No' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Municipality</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->municipality ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Barangay</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->barangay ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Scholarship Applications History -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                        Scholarship Application History
                    </h3>

                    <div class="space-y-3">
                        @forelse($student->applications as $app)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $app->scholarship->name }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Submitted on {{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                        @if($app->status === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-300
                                        @elseif($app->status === 'rejected') bg-slate-200 text-slate-800 border border-slate-300
                                        @elseif($app->status === 'incomplete') bg-rose-100 text-rose-800 border border-rose-300
                                        @else bg-amber-100 text-amber-800 border border-amber-300 @endif">
                                        {{ str_replace('_', ' ', $app->status) }}
                                    </span>

                                    <a href="{{ route('admin.applications.show', $app->id) }}" 
                                       class="px-3 py-1 bg-[#3B060F] text-white font-bold text-[11px] rounded-lg hover:bg-[#6B0F1A] transition">
                                        Review
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                No scholarship applications submitted by this student.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right Col: Active Scholarships Summary -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                        Active Scholarship Grants
                    </h3>

                    @forelse($student->scholars as $sch)
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 mb-3">
                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Awarded Program</span>
                            <h4 class="text-xs font-bold text-slate-900 mt-0.5">{{ $sch->scholarship->name }}</h4>
                            <div class="text-[11px] text-slate-600 mt-1">
                                Status: <span class="font-bold text-emerald-700 capitalize">{{ str_replace('_', ' ', $sch->status) }}</span>
                            </div>
                            <a href="{{ route('admin.scholars.show', $sch->id) }}" class="mt-2 inline-block text-[11px] font-bold text-[#3B060F] hover:underline">
                                View Scholar Profile &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Student currently holds no active scholarship grants.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Edit Student Profile & Password Modal -->
        <div x-show="editModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true"
             @keydown.escape.window="editModalOpen = false">
            
            <!-- Backdrop Overlay -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                 @click="editModalOpen = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
                <div x-show="editModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 w-full max-w-2xl border border-slate-200 dark:border-slate-800">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-5 bg-[#3B060F] text-white flex items-center justify-between border-b-2 border-[#FFC107]">
                        <div class="flex items-center space-x-3">
                            <div class="h-9 w-9 rounded-xl bg-[#FFC107] text-[#3B060F] font-extrabold flex items-center justify-center text-sm shadow-xs">
                                ✏️
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-white">
                                    Manage Student Profile & Password
                                </h3>
                                <p class="text-[11px] text-[#FFC107] font-medium">Update personal details or reset login credentials without current password</p>
                            </div>
                        </div>

                        <button type="button" @click="editModalOpen = false" class="text-slate-300 hover:text-white p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="p-6 space-y-6 max-h-[calc(85vh-130px)] overflow-y-auto">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Basic Identity Information -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <span class="h-5 w-5 rounded-full bg-[#6B0F1A] text-white text-[10px] font-bold flex items-center justify-center">1</span>
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-200">Personal Information</h4>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label for="first_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="first_name" 
                                           id="first_name" 
                                           value="{{ old('first_name', $student->user->first_name) }}" 
                                           required 
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border @error('first_name') border-red-500 @else border-slate-300 dark:border-slate-700 @enderror rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                    @error('first_name')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="middle_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Middle Name
                                    </label>
                                    <input type="text" 
                                           name="middle_name" 
                                           id="middle_name" 
                                           value="{{ old('middle_name', $student->user->middle_name) }}" 
                                           placeholder="Optional"
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                </div>

                                <div>
                                    <label for="last_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="last_name" 
                                           id="last_name" 
                                           value="{{ old('last_name', $student->user->last_name) }}" 
                                           required 
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border @error('last_name') border-red-500 @else border-slate-300 dark:border-slate-700 @enderror rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                    @error('last_name')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $student->user->email) }}" 
                                           required 
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border @error('email') border-red-500 @else border-slate-300 dark:border-slate-700 @enderror rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                    @error('email')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Contact Number
                                    </label>
                                    <input type="text" 
                                           name="contact_number" 
                                           id="contact_number" 
                                           value="{{ old('contact_number', $student->contact_number) }}" 
                                           placeholder="e.g. 09171234567"
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Academic Profile Details -->
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <span class="h-5 w-5 rounded-full bg-[#6B0F1A] text-white text-[10px] font-bold flex items-center justify-center">2</span>
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-200">Academic & Socioeconomic Information</h4>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label for="student_number" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Student ID Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="student_number" 
                                           id="student_number" 
                                           value="{{ old('student_number', $student->student_number) }}" 
                                           required 
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border @error('student_number') border-red-500 @else border-slate-300 dark:border-slate-700 @enderror rounded-xl font-mono text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-bold">
                                    @error('student_number')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="college" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        College
                                    </label>
                                    <select name="college" 
                                            id="college" 
                                            x-model="college"
                                            @change="if(availablePrograms.length > 0) { program = availablePrograms[0]; }"
                                            class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                        <option value="">-- Select College --</option>
                                        <option value="CAg">CAg - College of Agriculture</option>
                                        <option value="CHM">CHM - College of Hospitality Management</option>
                                        <option value="CICS">CICS - College of Information & Computing Sciences</option>
                                        <option value="CTE">CTE - College of Teacher Education</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="year_level" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Year Level
                                    </label>
                                    <select name="year_level" 
                                            id="year_level" 
                                            class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                        <option value="">-- Select Year Level --</option>
                                        <option value="1st Year" {{ old('year_level', $student->year_level) === '1st Year' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2nd Year" {{ old('year_level', $student->year_level) === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3rd Year" {{ old('year_level', $student->year_level) === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4th Year" {{ old('year_level', $student->year_level) === '4th Year' ? 'selected' : '' }}>4th Year</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="program" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Degree Program / Course
                                </label>
                                <input type="text" 
                                       name="program" 
                                       id="program" 
                                       x-model="program"
                                       value="{{ old('program', $student->program ?? $student->course) }}" 
                                       placeholder="e.g. BS Information Technology (BSIT)"
                                       class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="current_gwa" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Current GWA (1.00 - 5.00)
                                    </label>
                                    <input type="number" 
                                           step="0.01" 
                                           min="1.00" 
                                           max="5.00" 
                                           name="current_gwa" 
                                           id="current_gwa" 
                                           value="{{ old('current_gwa', $student->current_gwa) }}" 
                                           placeholder="e.g. 1.75"
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                </div>

                                <div>
                                    <label for="monthly_household_income" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Monthly Household Income (₱)
                                    </label>
                                    <input type="number" 
                                           step="100" 
                                           name="monthly_household_income" 
                                           id="monthly_household_income" 
                                           value="{{ old('monthly_household_income', $student->monthly_household_income) }}" 
                                           placeholder="e.g. 15000"
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="municipality" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Municipality / Town
                                    </label>
                                    <input type="text" 
                                           name="municipality" 
                                           id="municipality" 
                                           value="{{ old('municipality', $student->municipality) }}" 
                                           placeholder="e.g. Lal-lo"
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                </div>

                                <div>
                                    <label for="barangay" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Barangay
                                    </label>
                                    <input type="text" 
                                           name="barangay" 
                                           id="barangay" 
                                           value="{{ old('barangay', $student->barangay) }}" 
                                           placeholder="e.g. Centro"
                                           class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 pt-1">
                                <input type="checkbox" 
                                       name="is_4ps" 
                                       id="is_4ps" 
                                       value="1" 
                                       {{ old('is_4ps', $student->is_4ps) ? 'checked' : '' }} 
                                       class="h-4 w-4 text-[#6B0F1A] rounded border-slate-300 focus:ring-[#6B0F1A] cursor-pointer">
                                <label for="is_4ps" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                                    Household is a 4Ps Beneficiary
                                </label>
                            </div>
                        </div>

                        <!-- Section 3: Admin Password Override (Without Current Password) -->
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center gap-2 border-b border-amber-200 dark:border-amber-900/60 pb-2">
                                <span class="h-5 w-5 rounded-full bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center">🔑</span>
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-amber-800 dark:text-amber-400">Admin Password Management</h4>
                            </div>

                            <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl text-xs text-amber-900 dark:text-amber-200">
                                <p class="font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Admin Password Override (No Current Password Required)
                                </p>
                                <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-0.5 leading-relaxed">
                                    As an administrator, you can reset or update this student's login password directly. Leave these fields blank to keep the student's existing password unchanged.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="edit_student_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        New Password
                                    </label>
                                    <x-password-input 
                                        name="password" 
                                        id="edit_student_password" 
                                        autocomplete="new-password"
                                        placeholder="Leave blank to keep unchanged" 
                                        class="px-3 py-2 bg-slate-50 dark:bg-slate-800 text-xs"
                                    />
                                    @error('password')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="edit_student_password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Confirm New Password
                                    </label>
                                    <x-password-input 
                                        name="password_confirmation" 
                                        id="edit_student_password_confirmation" 
                                        autocomplete="new-password"
                                        placeholder="Confirm new password" 
                                        class="px-3 py-2 bg-slate-50 dark:bg-slate-800 text-xs"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                            <button type="button" 
                                    @click="editModalOpen = false" 
                                    class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 transition cursor-pointer border border-slate-200 dark:border-slate-700">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-[#3B060F] hover:bg-[#6B0F1A] px-5 py-2.5 text-xs font-extrabold text-white shadow-md transition cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Student Profile
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

</x-app-layout>

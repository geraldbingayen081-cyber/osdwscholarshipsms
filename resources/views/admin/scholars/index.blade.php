<x-app-layout title="Scholar Grantees - CSU Lal-lo Admin">

<div x-data="{ 
    addGranteeModalOpen: false, 
    studentSearch: '', 
    selectedStudentId: '{{ old('student_id', '') }}',
    selectedStudentText: '',
    studentsList: {{ Js::from($students->map(fn($s) => [
        'id' => $s->id,
        'student_number' => $s->student_number,
        'name' => $s->user->full_name ?? 'Student',
        'email' => $s->user->email ?? '',
        'course' => $s->course ?? '',
        'year_level' => $s->year_level ?? '',
        'college' => $s->college_name ?? ''
    ])) }},
    get filteredStudents() {
        if (!this.studentSearch) return this.studentsList.slice(0, 30);
        const q = this.studentSearch.toLowerCase();
        return this.studentsList.filter(s => 
            s.name.toLowerCase().includes(q) || 
            s.student_number.toLowerCase().includes(q) || 
            s.course.toLowerCase().includes(q) ||
            s.email.toLowerCase().includes(q)
        ).slice(0, 30);
    },
    selectStudent(student) {
        this.selectedStudentId = student.id;
        this.selectedStudentText = student.name + ' (' + student.student_number + ' - ' + student.course + ')';
        this.studentSearch = '';
    }
}" 
x-init="
    if (selectedStudentId) {
        const found = studentsList.find(s => s.id == selectedStudentId);
        if (found) selectedStudentText = found.name + ' (' + found.student_number + ' - ' + found.course + ')';
    }
">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Scholar Grantees Roster</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Comprehensive roster of all enrolled student grantees across all active and completed scholarship programs.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Add Student Grantee (Direct Enrollment) Button -->
            <button type="button" 
                    @click="addGranteeModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md shrink-0 cursor-pointer border border-[#FFC107]/40">
                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Add Student Grantee
            </button>

            <a href="{{ route('admin.compliance.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white font-extrabold text-xs rounded-xl hover:bg-emerald-800 transition shadow-md shrink-0 cursor-pointer border border-[#FFC107]/40">
                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Request Compliance
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <a href="{{ route('admin.scholars.index') }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ empty($status) ? 'bg-[#3B060F] text-white border-[#3B060F] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold uppercase tracking-wider {{ empty($status) ? 'text-[#FFC107]' : 'text-slate-500' }}">Total Grantees</span>
            <span class="text-2xl font-extrabold mt-1">{{ number_format($stats['total']) }}</span>
        </a>

        <!-- Active -->
        <a href="{{ route('admin.scholars.index', array_merge(request()->except('page'), ['status' => 'active'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'active' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'active' ? 'text-emerald-100' : 'text-slate-500' }}">Active</span>
            <span class="text-2xl font-extrabold mt-1">{{ number_format($stats['active']) }}</span>
        </a>

        <!-- Completed -->
        <a href="{{ route('admin.scholars.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'completed' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'completed' ? 'text-blue-100' : 'text-slate-500' }}">Completed</span>
            <span class="text-2xl font-extrabold mt-1">{{ number_format($stats['completed']) }}</span>
        </a>

        <!-- Terminated -->
        <a href="{{ route('admin.scholars.index', array_merge(request()->except('page'), ['status' => 'terminated'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'terminated' ? 'bg-rose-700 text-white border-rose-700 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'terminated' ? 'text-rose-100' : 'text-slate-500' }}">Terminated</span>
            <span class="text-2xl font-extrabold mt-1">{{ number_format($stats['terminated']) }}</span>
        </a>
    </div>

    <!-- Filter & Search Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-4 mb-6">
        <form method="GET" action="{{ route('admin.scholars.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search grantee name, email, or Student ID (00-00000)..." 
                       {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                       oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                       class="w-full py-2 pl-9 pr-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
            </div>

            <!-- Scholarship Program Filter formatted as SY - Name of Scholarship -->
            <div class="sm:col-span-6 flex items-center gap-2">
                <select name="scholarship_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-100 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                    <option value="">All Scholarship Programs</option>
                    @foreach($scholarships as $sch)
                        <option value="{{ $sch->id }}" {{ $scholarshipId == $sch->id ? 'selected' : '' }}>
                            {{ $sch->school_year_label }} - {{ $sch->name }}
                        </option>
                    @endforeach
                </select>
                @if($search || $scholarshipId || $status)
                    <a href="{{ route('admin.scholars.index') }}" class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition shrink-0" title="Reset Filters">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Student Grantees Table List Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3B060F] dark:text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Student Grantees Roster
            </h3>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                Showing {{ $scholars->firstItem() ?? 0 }} to {{ $scholars->lastItem() ?? 0 }} of {{ $scholars->total() }} Grantees
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase border-b border-slate-200 dark:border-slate-700 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Student Grantee</th>
                        <th class="px-6 py-3.5">Student ID</th>
                        <th class="px-6 py-3.5">Course & Year</th>
                        <th class="px-6 py-3.5">Scholarship Program (SY)</th>
                        <th class="px-6 py-3.5">Scholarship Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($scholars as $scholar)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition">
                            <!-- Student Name & Avatar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($scholar->student->user->profile_photo_url)
                                        <img src="{{ $scholar->student->user->profile_photo_url }}" alt="{{ $scholar->student->user->full_name }}" class="h-9 w-9 rounded-full object-cover border border-[#6B0F1A] shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-full bg-[#3B060F] text-[#FFC107] font-bold flex items-center justify-center text-xs shrink-0 border border-[#FFC107]/30">
                                            {{ strtoupper(substr($scholar->student->user->first_name, 0, 1) . substr($scholar->student->user->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                                            {{ $scholar->student->user->full_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-400 font-medium">
                                            {{ $scholar->student->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Student ID -->
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                                    {{ $scholar->student->student_number }}
                                </span>
                            </td>

                            <!-- Program / Course & Year Level -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                    {{ $scholar->student->course }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                    {{ $scholar->student->year_level }} • <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $scholar->student->college->value ?? $scholar->student->college }}</span>
                                </div>
                            </td>

                            <!-- Scholarship Program (SY - Name) & Coverage -->
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 dark:text-white text-xs">
                                    {{ $scholar->scholarship->school_year_label }} - {{ $scholar->scholarship->name }}
                                </div>
                                <div class="text-[11px] font-semibold mt-0.5 flex items-center gap-1.5 {{ $scholar->scholarship->isContinuing() ? 'text-emerald-700 dark:text-emerald-400' : 'text-blue-700 dark:text-blue-400' }}">
                                    <span>{{ $scholar->scholarship->coverage_type_label }}</span>
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span class="text-slate-400 dark:text-slate-400 font-normal">{{ $scholar->scholarship->provider }}</span>
                                </div>
                            </td>

                            <!-- Scholarship Status -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    @if($scholar->status === 'active') bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800
                                    @elseif($scholar->status === 'completed') bg-blue-100 text-blue-800 border border-blue-300 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800
                                    @elseif($scholar->status === 'for_renewal') bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800
                                    @else bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800 @endif">
                                    {{ str_replace('_', ' ', $scholar->status) }}
                                </span>
                            </td>

                            <!-- View Scholar Profile & Standing Quick Action -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.scholars.update-status', $scholar->id) }}" class="inline-block">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="py-1 px-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-[11px] font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none cursor-pointer">
                                            <option value="active" {{ $scholar->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="for_renewal" {{ $scholar->status === 'for_renewal' ? 'selected' : '' }}>For Renewal</option>
                                            <option value="completed" {{ $scholar->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="terminated" {{ $scholar->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                        </select>
                                    </form>

                                    <a href="{{ route('admin.scholars.show', $scholar->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-[#3B060F] text-white font-bold rounded-lg text-xs hover:bg-[#6B0F1A] transition shadow-xs cursor-pointer shrink-0">
                                        View Profile
                                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 text-xs">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-200">No Scholar Grantees Found</h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">No student grantees match your current search or scholarship filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($scholars->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $scholars->links() }}
            </div>
        @endif
    </div>

    <!-- Direct Student Grantee Enrollment Modal -->
    <div x-show="addGranteeModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="addGranteeModalOpen = false"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="addGranteeModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="addGranteeModalOpen = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <!-- Modal Box -->
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="addGranteeModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl border border-slate-200 dark:border-slate-800 transition-all">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-[#3B060F] to-[#5a0914] text-white flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-[#FFC107]/20 rounded-xl border border-[#FFC107]/30 text-[#FFC107]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white tracking-tight">
                                Add Student Grantee
                            </h3>
                            <p class="text-xs text-amber-200/90 font-medium">
                                Direct Enrollment • No application submission required
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="addGranteeModalOpen = false" 
                            class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('admin.scholars.store') }}" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="student_id" :value="selectedStudentId" required>

                    <!-- Information Banner -->
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-800 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
                            This registers the student immediately as an official grantee under the selected scholarship program. A formal online application and document verification process is bypassed.
                        </p>
                    </div>

                    <!-- Step 1: Select Student -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            1. Select Student <span class="text-rose-500">*</span>
                        </label>

                        <!-- Selected Student Display Card -->
                        <div x-show="selectedStudentId" class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    <svg class="w-5 h-5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="truncate">
                                    <div class="font-extrabold text-slate-900 dark:text-white text-xs truncate" x-text="selectedStudentText"></div>
                                    <div class="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold">Selected Student Grantee</div>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="selectedStudentId = ''; selectedStudentText = ''" 
                                    class="text-xs font-bold text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300 hover:bg-rose-100/60 dark:hover:bg-rose-950/50 px-2.5 py-1 rounded-lg transition shrink-0 cursor-pointer">
                                Change
                            </button>
                        </div>

                        <!-- Student Search & Selection Dropdown Area -->
                        <div x-show="!selectedStudentId" class="space-y-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" 
                                       x-model="studentSearch" 
                                       placeholder="Type student name, student ID (e.g. 21-00123), or course..." 
                                       class="w-full py-2.5 pl-9 pr-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none font-medium">
                            </div>

                            <div class="max-h-48 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded-xl divide-y divide-slate-100 dark:divide-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                                <template x-for="st in filteredStudents" :key="st.id">
                                    <div @click="selectStudent(st)" 
                                         class="p-2.5 hover:bg-amber-50/70 dark:hover:bg-slate-700/60 hover:border-l-4 hover:border-l-[#3B060F] dark:hover:border-l-[#FFC107] transition cursor-pointer flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="text-xs font-extrabold text-slate-900 dark:text-white truncate" x-text="st.name"></div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="st.student_number"></span> • <span x-text="st.course"></span> (<span x-text="st.year_level"></span>)
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded border border-slate-200 dark:border-slate-700 shrink-0">
                                            Select
                                        </span>
                                    </div>
                                </template>
                                <div x-show="filteredStudents.length === 0" class="p-4 text-center text-xs text-slate-400 dark:text-slate-500">
                                    No students match search query.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Select Scholarship Program -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            2. Scholarship Program <span class="text-rose-500">*</span>
                        </label>
                        <select name="scholarship_id" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                            <option value="">-- Choose Scholarship Program --</option>
                            @foreach($scholarships as $sch)
                                <option value="{{ $sch->id }}" {{ old('scholarship_id') == $sch->id ? 'selected' : '' }}>
                                    {{ $sch->school_year_label }} — {{ $sch->name }} ({{ $sch->coverage_type_label }} | {{ $sch->provider }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 3: Status & Award Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                                3. Initial Status <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active Grantee</option>
                                <option value="for_renewal" {{ old('status') === 'for_renewal' ? 'selected' : '' }}>For Renewal (Action Required)</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed / Graduated</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                                4. Award / Effective Date
                            </label>
                            <input type="date" 
                                   name="approved_at" 
                                   value="{{ old('approved_at', date('Y-m-d')) }}" 
                                   class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>
                    </div>

                    <!-- Step 4: Remarks / Reason for Direct Enrollment -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1.5">
                            5. Administrative Remarks / Endorsement Note <span class="text-slate-400 dark:text-slate-500 font-normal lowercase">(optional)</span>
                        </label>
                        <textarea name="remarks" 
                                  rows="2" 
                                  placeholder="e.g. Endorsed by CHED..." 
                                  class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none leading-relaxed">{{ old('remarks') }}</textarea>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" 
                                @click="addGranteeModalOpen = false" 
                                class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="!selectedStudentId" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#3B060F] hover:bg-[#6B0F1A] text-white text-xs font-extrabold rounded-xl transition shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed border border-[#FFC107]/30">
                            <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Enroll Grantee
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

</x-app-layout>


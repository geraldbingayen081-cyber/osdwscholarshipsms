<x-app-layout title="Student Profile - CSU Lal-lo Admin">

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
    </div>

    <!-- Student Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Profile Info & Applications History -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Personal Info Card -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                <div class="flex items-center space-x-4 border-b border-slate-100 pb-4 mb-4">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Full Name</span>
                        <span class="font-extrabold text-slate-900 text-sm mt-0.5 block">{{ $student->user->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Student ID</span>
                        <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $student->student_number }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Degree Program / Course</span>
                        <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->course }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Year Level</span>
                        <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->year_level }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Email Address</span>
                        <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->user->email }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Contact Number</span>
                        <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $student->contact_number ?? 'N/A' }}</span>
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

</x-app-layout>

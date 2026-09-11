<x-app-layout title="Admin Dashboard - CSU–Lal-lo">
    


    <!-- 2. METRIC CARDS ROW (Matching Screenshot Layout) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Card 1: Registered Students -->
        <a href="{{ route('admin.students.index') }}" class="group bg-white rounded-xl shadow-xs border border-slate-200 p-5 flex items-center justify-between hover:shadow-md hover:border-slate-300 transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Registered Students</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($stats['total_students']) }}</h3>
                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5">+5 this month</p>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- Card 2: Active Scholarships -->
        <a href="{{ route('admin.scholarships.index') }}" class="group bg-white rounded-xl shadow-xs border border-slate-200 p-5 flex items-center justify-between hover:shadow-md hover:border-slate-300 transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-emerald-100 text-[#6B0F1A] flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Active Scholarships</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($stats['active_scholarships']) }}</h3>
                    <p class="text-[11px] font-bold text-emerald-600 mt-0.5">+2 active programs</p>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- Card 3: Pending Applications -->
        <a href="{{ route('admin.applications.index') }}" class="group bg-white rounded-xl shadow-xs border border-slate-200 p-5 flex items-center justify-between hover:shadow-md hover:border-slate-300 transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Pending Review</p>
                    <h3 class="text-2xl font-extrabold text-amber-700 mt-0.5">{{ number_format($stats['pending_applications']) }}</h3>
                    <p class="text-[11px] font-bold text-amber-600 mt-0.5">Needs action</p>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- Card 4: Active Scholars -->
        <a href="{{ route('admin.scholars.index') }}" class="group bg-white rounded-xl shadow-xs border border-slate-200 p-5 flex items-center justify-between hover:shadow-md hover:border-slate-300 transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-[#FFC107] text-[#3B060F] flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Active Scholars</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($stats['active_scholars']) }}</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Enrolled</p>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>

    <!-- 3. MAIN CONTENT GRID (Left 2 Cols + Right Widget Col) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- LEFT & MIDDLE COLUMNS (Scholarships List & Recent Applications Table) -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Scholarship Programs Box -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Scholarship Programs</h3>
                        <p class="text-xs text-slate-500">Active institutional and government financial assistance programs</p>
                    </div>
                    <a href="{{ route('admin.scholarships.index') }}" class="px-3.5 py-1.5 bg-[#3B060F] text-white hover:bg-[#6B0F1A] font-extrabold text-[11px] rounded-xl transition shadow-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        View All Programs
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3">Program Name</th>
                                <th class="px-6 py-3">Provider</th>
                                <th class="px-6 py-3">School Year</th>
                                <th class="px-6 py-3 text-center">Available Slots</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($scholarships as $scholarship)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        <a href="{{ route('admin.scholarships.show', $scholarship->id) }}" class="hover:text-[#7B1113]">
                                            {{ $scholarship->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $scholarship->provider ?? 'CSU OSDW' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">
                                        {{ $scholarship->school_year ?? $scholarship->school_year_label ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $scholarship->remaining_slots > 0 ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                            {{ $scholarship->remaining_slots }}/{{ $scholarship->available_slots }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase 
                                            @if($scholarship->status === 'open') bg-emerald-100 text-emerald-800 border border-emerald-200
                                            @elseif($scholarship->status === 'closed') bg-slate-100 text-slate-700 border border-slate-200
                                            @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                            {{ $scholarship->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.scholarships.show', $scholarship->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-[11px] hover:bg-slate-200 transition">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-xs">
                                        No scholarship programs created yet. <a href="{{ route('admin.scholarships.create') }}" class="text-[#7B1113] font-bold underline">Add one now</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Recent Applications Box -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800">Recent Student Applications</h3>
                    <a href="{{ route('admin.applications.index') }}" class="px-3.5 py-1.5 bg-[#3B060F] text-white hover:bg-[#6B0F1A] font-extrabold text-[11px] rounded-xl transition shadow-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View All Applications
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3">Student Name</th>
                                <th class="px-6 py-3">Student ID</th>
                                <th class="px-6 py-3">Scholarship</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($recentApplications as $app)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        {{ $app->student->user->full_name }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-mono">
                                        {{ $app->student->student_number }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">
                                        {{ $app->scholarship->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold capitalize 
                                            @if($app->status === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-200
                                            @elseif($app->status === 'rejected') bg-red-100 text-red-800 border border-red-200
                                            @elseif($app->status === 'incomplete') bg-rose-100 text-rose-800 border border-rose-200
                                            @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                            {{ str_replace('_', ' ', $app->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.applications.show', $app->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#3B060F] text-white font-extrabold rounded-xl text-[11px] hover:bg-[#6B0F1A] transition shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Review App
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 text-xs">
                                        No student applications submitted yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- RIGHT SIDEBAR COLUMN (Quick Admin Actions) -->
        <div class="space-y-6">
            
            <!-- Quick Actions Widget -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5">
                <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Admin Quick Actions</h3>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.scholarships.create') }}" class="p-3.5 rounded-xl bg-[#6B0F1A] text-white hover:bg-[#500A15] transition flex flex-col items-center justify-center text-center shadow-xs">
                        <svg class="w-6 h-6 text-[#FFC107] mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-[11px] font-bold">New Scholarship</span>
                    </a>

                    <a href="{{ route('admin.applications.index') }}" class="p-3.5 rounded-xl bg-[#FFC107] text-[#3B060F] hover:bg-amber-400 transition flex flex-col items-center justify-center text-center shadow-xs">
                        <svg class="w-6 h-6 text-[#3B060F] mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[11px] font-extrabold">Review Apps</span>
                    </a>

                    <a href="{{ route('admin.compliance.index') }}" class="p-3.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition flex flex-col items-center justify-center text-center">
                        <svg class="w-6 h-6 text-slate-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="text-[11px] font-bold">Compliance Requests</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="p-3.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition flex flex-col items-center justify-center text-center">
                        <svg class="w-6 h-6 text-slate-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[11px] font-bold">View Reports</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>

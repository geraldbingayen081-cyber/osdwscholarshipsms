<x-app-layout title="Scholar Grantees - CSU Lal-lo Admin">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Scholar Grantees Roster</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Comprehensive roster of all enrolled student grantees across all active and completed scholarship programs.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.compliance.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white font-extrabold text-xs rounded-xl hover:bg-emerald-800 transition shadow-md shrink-0 cursor-pointer border border-[#FFC107]/40">
                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Request Compliance
            </a>

            <a href="{{ route('admin.export.scholars', request()->all()) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md shrink-0 cursor-pointer">
                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Roster (CSV)
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
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-4 mb-6">
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
                       placeholder="Search grantee name, email, or Student ID (e.g. 26-32424)..." 
                       {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                       oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                       class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
            </div>

            <!-- Scholarship Program Filter formatted as SY - Name of Scholarship -->
            <div class="sm:col-span-6 flex items-center gap-2">
                <select name="scholarship_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                    <option value="">All Scholarship Programs</option>
                    @foreach($scholarships as $sch)
                        <option value="{{ $sch->id }}" {{ $scholarshipId == $sch->id ? 'selected' : '' }}>
                            {{ $sch->school_year_label }} - {{ $sch->name }}
                        </option>
                    @endforeach
                </select>
                @if($search || $scholarshipId || $status)
                    <a href="{{ route('admin.scholars.index') }}" class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition shrink-0" title="Reset Filters">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Student Grantees Table List Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3B060F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Student Grantees Roster
            </h3>
            <span class="text-xs font-semibold text-slate-500">
                Showing {{ $scholars->firstItem() ?? 0 }} to {{ $scholars->lastItem() ?? 0 }} of {{ $scholars->total() }} Grantees
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-100/70 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Student Grantee</th>
                        <th class="px-6 py-3">Student ID</th>
                        <th class="px-6 py-3">Course & Year</th>
                        <th class="px-6 py-3">Scholarship Program (SY)</th>
                        <th class="px-6 py-3">Scholarship Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($scholars as $scholar)
                        <tr class="hover:bg-slate-50/80 transition">
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
                                        <div class="font-extrabold text-slate-900 text-sm">
                                            {{ $scholar->student->user->full_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium">
                                            {{ $scholar->student->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Student ID -->
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-800 text-xs bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                    {{ $scholar->student->student_number }}
                                </span>
                            </td>

                            <!-- Program / Course & Year Level -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-xs">
                                    {{ $scholar->student->course }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                    {{ $scholar->student->year_level }} • <span class="font-semibold text-slate-700">{{ $scholar->student->college->value ?? $scholar->student->college }}</span>
                                </div>
                            </td>

                            <!-- Scholarship Program (SY - Name) & Coverage -->
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-xs">
                                    {{ $scholar->scholarship->school_year_label }} - {{ $scholar->scholarship->name }}
                                </div>
                                <div class="text-[11px] font-semibold mt-0.5 flex items-center gap-1.5 {{ $scholar->scholarship->isContinuing() ? 'text-emerald-700' : 'text-blue-700' }}">
                                    <span>{{ $scholar->scholarship->coverage_type_label }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-400 font-normal">{{ $scholar->scholarship->provider }}</span>
                                </div>
                            </td>

                            <!-- Scholarship Status -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    @if($scholar->status === 'active') bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @elseif($scholar->status === 'completed') bg-blue-100 text-blue-800 border border-blue-300
                                    @else bg-rose-100 text-rose-800 border border-rose-300 @endif">
                                    {{ str_replace('_', ' ', $scholar->status) }}
                                </span>
                            </td>

                            <!-- View Scholar Profile & Standing Quick Action -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.scholars.update-status', $scholar->id) }}" class="inline-block">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="py-1 px-2.5 bg-slate-100 border border-slate-300 rounded-lg text-[11px] font-bold text-slate-700 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none cursor-pointer">
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
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <h3 class="text-sm font-extrabold text-slate-800">No Scholar Grantees Found</h3>
                                    <p class="text-xs text-slate-400 mt-1">No student grantees match your current search or scholarship filter criteria.</p>
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

</x-app-layout>

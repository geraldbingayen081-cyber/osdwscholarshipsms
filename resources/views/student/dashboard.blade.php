<x-app-layout title="Student Dashboard - CSU–Lal-lo">
    
    <!-- 2. STREAMLINED METRIC CARDS ROW (Clean & Uncluttered) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Card 1: Total Applications -->
        <a href="{{ route('student.applications.index') }}" class="group bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 flex items-center justify-between hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-[#6B0F1A] dark:text-emerald-300 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Applications</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $stats['total_applications'] }}</h3>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- Card 2: Pending Applications -->
        <a href="{{ route('student.applications.index') }}" class="group bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 flex items-center justify-between hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pending Review</p>
                    <h3 class="text-2xl font-extrabold text-amber-700 dark:text-amber-400 mt-0.5">{{ $stats['pending_applications'] }}</h3>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- Card 3: Approved Applications -->
        <a href="{{ route('student.applications.index') }}" class="group bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 flex items-center justify-between hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Approved Grants</p>
                    <h3 class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400 mt-0.5">{{ $stats['approved_applications'] }}</h3>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <!-- Card 4: Active Scholarships -->
        <a href="{{ route('student.applications.index') }}" class="group bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 flex items-center justify-between hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition">
            <div class="flex items-center space-x-4">
                <div class="h-11 w-11 rounded-xl bg-[#FFC107] text-[#3B060F] flex items-center justify-center font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active Scholar</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $stats['active_scholarships'] }}</h3>
                </div>
            </div>
            <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 group-hover:bg-[#3B060F] group-hover:text-white flex items-center justify-center transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>

    <!-- PERIODIC COMPLIANCE NOTIFICATION CARDS -->
    @if(isset($myCompliances) && $myCompliances->count() > 0)
        @foreach($myCompliances as $comp)
            @php
                $cr = $comp->complianceRequest;
                $isPending = in_array($comp->status, ['not_submitted', 'partially_submitted', 'needs_correction', 'overdue']);
            @endphp
            <div class="mb-6 rounded-2xl p-5 border shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition
                @if($comp->status === 'needs_correction') bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-700
                @elseif($comp->status === 'completed') bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-700
                @elseif($comp->status === 'overdue') bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-700
                @else bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 @endif">
                
                <div class="flex items-start space-x-4">
                    <div class="h-12 w-12 rounded-2xl flex items-center justify-center font-bold text-xl shrink-0 shadow-xs
                        @if($comp->status === 'needs_correction') bg-amber-200 text-amber-900
                        @elseif($comp->status === 'completed') bg-emerald-600 text-white
                        @elseif($comp->status === 'overdue') bg-rose-200 text-rose-900
                        @else bg-[#3B060F] text-[#FFC107] @endif">
                        📋
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                @if($comp->status === 'completed') bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-700
                                @elseif($comp->status === 'needs_correction') bg-amber-200 text-amber-900 border border-amber-400 font-black animate-pulse dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-600
                                @elseif($comp->status === 'overdue') bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-700
                                @elseif($comp->status === 'under_review' || $comp->status === 'submitted') bg-indigo-100 text-indigo-800 border border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-700
                                @else bg-slate-100 text-slate-800 border border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 @endif">
                                {{ str_replace('_', ' ', $comp->status) }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                {{ $cr->semester }} AY {{ $cr->school_year }}
                            </span>
                        </div>

                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-1">
                            {{ $cr->title }}
                        </h3>

                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Program: <strong class="text-slate-800 dark:text-slate-200">{{ $cr->scholarship->name }}</strong> • 
                            Deadline: <strong class="{{ $cr->isPastDeadline() ? 'text-red-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $cr->deadline->format('M d, Y') }}</strong>
                            @if(!$cr->isPastDeadline())
                                <span class="text-slate-400 dark:text-slate-500">({{ $cr->days_remaining }} left)</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('student.compliance.show', $cr->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 font-extrabold text-xs rounded-xl transition shadow-sm
                        @if($comp->status === 'needs_correction') bg-amber-600 text-white hover:bg-amber-700
                        @elseif($comp->status === 'completed') bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700
                        @else bg-[#3B060F] text-white hover:bg-[#6B0F1A] border border-[#FFC107]/40 @endif">
                        @if($comp->status === 'needs_correction')
                            ⚠️ Resubmit Required Files
                        @elseif($comp->status === 'completed')
                            View Submitted Files
                        @else
                            Submit Requirements &rarr;
                        @endif
                    </a>
                </div>
            </div>
        @endforeach
    @endif

    <!-- 3. MAIN DASHBOARD GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT & MIDDLE COLUMNS (Recent Applications + Available Scholarships) -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Recent Applications Box -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">My Active Applications</h3>
                    <a href="{{ route('student.applications.index') }}" class="text-xs font-extrabold text-[#7B1113] dark:text-[#FFC107] hover:underline">
                        View All &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-3">Scholarship Program</th>
                                <th class="px-6 py-3">Status Tracking</th>
                                <th class="px-6 py-3">Submission Date</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($myApplications as $app)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                        {{ $app->scholarship->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $st = is_object($app->status) ? $app->status->value : $app->status;
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                            @if($st === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-700
                                            @elseif($st === 'deficient') bg-amber-100 text-amber-900 border border-amber-400 font-extrabold animate-pulse dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700
                                            @elseif($st === 'under_review') bg-indigo-100 text-indigo-800 border border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-700
                                            @elseif($st === 'eligible') bg-teal-100 text-teal-800 border border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-700
                                            @elseif($st === 'rejected') bg-red-100 text-red-800 border border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-700
                                            @else bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-700 @endif">
                                            {{ $st === 'deficient' ? 'Action Required / Deficient' : str_replace('_', ' ', $st) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-medium">
                                        {{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if((is_object($app->status) ? $app->status->value : $app->status) === 'deficient')
                                            <a href="{{ route('student.applications.resolve', $app->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold rounded-xl text-[11px] transition shadow-md">
                                                ⚠️ Resolve Copy
                                            </a>
                                        @else
                                            <a href="{{ route('student.applications.show', $app->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#7B1113] text-white font-extrabold rounded-xl text-[11px] hover:bg-[#540B0D] transition shadow-xs">
                                                View Details
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 text-xs">
                                        You have not submitted any scholarship applications yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Available Scholarships Box -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Available Scholarship Grants</h3>
                    <a href="{{ route('student.scholarships.index') }}" class="px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-[#7B1113] dark:text-[#FFC107] hover:bg-[#7B1113] hover:text-white dark:hover:bg-[#FFC107] dark:hover:text-[#3B060F] font-extrabold text-[11px] rounded-xl transition shadow-2xs inline-flex items-center gap-1.5">
                        Browse Catalog
                    </a>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($availableScholarships as $s)
                        @php
                            $gwaCutoffFailed = !is_null($s->min_gwa) && $student->current_gwa && $student->current_gwa > $s->min_gwa;
                            $incomeFailed = !is_null($s->max_household_income) && $student->monthly_household_income && $student->monthly_household_income > $s->max_household_income;
                            $isPreQualified = !$gwaCutoffFailed && !$incomeFailed;
                        @endphp
                        <div class="p-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-xl bg-[#7B1113]/10 dark:bg-[#FFC107]/10 text-[#7B1113] dark:text-[#FFC107] flex items-center justify-center font-bold shrink-0">
                                    🎓
                                </div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900 dark:text-white">{{ $s->name }}</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Provider: <strong class="text-slate-700 dark:text-slate-300">{{ $s->provider }}</strong> • Slots: {{ $s->available_slots }}</p>
                                    
                                    <!-- Pre-qualification flags -->
                                    @if($gwaCutoffFailed)
                                        <span class="mt-1 inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            Requires GWA ≤ {{ number_format($s->min_gwa, 2) }} (Your GWA: {{ number_format($student->current_gwa, 2) }})
                                        </span>
                                    @elseif($incomeFailed)
                                        <span class="mt-1 inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            Income exceeds ₱{{ number_format($s->max_household_income, 2) }} limit
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if(!empty($hasActiveScholarship))
                                <button type="button" disabled class="inline-flex items-center px-3 py-1.5 bg-amber-100 text-amber-900 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700 font-extrabold rounded-xl text-xs cursor-not-allowed shrink-0" title="You already have an active scholarship">
                                    Already a Scholar
                                </button>
                            @elseif($isPreQualified)
                                <a href="{{ route('student.applications.create', $s->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#7B1113] text-white font-extrabold rounded-xl text-xs hover:bg-[#540B0D] transition shadow-sm shrink-0">
                                    Apply Now &rarr;
                                </a>
                            @else
                                <button type="button" disabled class="inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-extrabold rounded-xl text-xs cursor-not-allowed shrink-0">
                                    Not Eligible
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500 dark:text-slate-400 text-xs">
                            No open scholarships available at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- RIGHT SIDEBAR COLUMN (Notifications + Quick Actions) -->
        <div class="space-y-6">
            
            <!-- Quick Actions Widget -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-5">
                <h3 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">Quick Actions</h3>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('student.scholarships.index') }}" class="p-3.5 rounded-xl bg-[#6B0F1A] text-white hover:bg-[#500A15] transition flex flex-col items-center justify-center text-center shadow-xs">
                        <svg class="w-5 h-5 text-[#FFC107] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="text-[11px] font-bold">Browse Catalog</span>
                    </a>

                    <a href="{{ route('student.scholarships.index') }}" class="p-3.5 rounded-xl bg-[#FFC107] text-[#3B060F] hover:bg-amber-400 transition flex flex-col items-center justify-center text-center shadow-xs">
                        <svg class="w-5 h-5 text-[#3B060F] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[11px] font-extrabold">Apply Now</span>
                    </a>

                    <a href="{{ route('student.profile.show') }}" class="p-3.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition flex flex-col items-center justify-center text-center">
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-[11px] font-bold">My Profile</span>
                    </a>

                    <a href="{{ route('student.applications.index') }}" class="p-3.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition flex flex-col items-center justify-center text-center">
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 022-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="text-[11px] font-bold">My Apps</span>
                    </a>
                </div>
            </div>

            <!-- Notifications Widget -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-2">
                        Recent Notifications
                    </h3>
                </div>

                <div class="space-y-3">
                    <div class="flex items-start space-x-3 text-xs">
                        <div class="h-2 w-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">Application Submitted</p>
                            <p class="text-slate-500 dark:text-slate-400 text-[11px]">Your scholarship application was received by OSDW.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>

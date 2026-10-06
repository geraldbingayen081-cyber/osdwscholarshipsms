<x-app-layout title="Manage Welfare Cases - CSU Lal-lo Admin">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Student Welfare Cases</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
                Review reported student concerns, assess financial assistance needs, and issue scholarship referrals.
            </p>
        </div>
    </div>

    <!-- Metric Tabs Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 mb-6">
        <!-- All Cases -->
        <a href="{{ route('admin.welfare-cases.index') }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ empty($status) ? 'bg-[#3B060F] text-white border-[#3B060F] shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ empty($status) ? 'text-[#FFC107]' : 'text-slate-400' }}">All Cases</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['total']) }}</span>
        </a>

        <!-- Open -->
        <a href="{{ route('admin.welfare-cases.index', array_merge(request()->except('page'), ['status' => 'Open'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'Open' ? 'bg-amber-600 text-white border-amber-600 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $status === 'Open' ? 'text-amber-100' : 'text-slate-400' }}">Open</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['open']) }}</span>
        </a>

        <!-- Under Assessment -->
        <a href="{{ route('admin.welfare-cases.index', array_merge(request()->except('page'), ['status' => 'Under Assessment'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'Under Assessment' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $status === 'Under Assessment' ? 'text-blue-100' : 'text-slate-400' }}">Under Assessment</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['under_assessment']) }}</span>
        </a>

        <!-- Referred -->
        <a href="{{ route('admin.welfare-cases.index', array_merge(request()->except('page'), ['status' => 'Referred'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'Referred' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $status === 'Referred' ? 'text-emerald-200' : 'text-slate-400' }}">Referred</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['referred']) }}</span>
        </a>

        <!-- For Follow-up -->
        <a href="{{ route('admin.welfare-cases.index', array_merge(request()->except('page'), ['status' => 'For Follow-up'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'For Follow-up' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $status === 'For Follow-up' ? 'text-purple-100' : 'text-slate-400' }}">For Follow-up</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['for_followup']) }}</span>
        </a>

        <!-- Resolved -->
        <a href="{{ route('admin.welfare-cases.index', array_merge(request()->except('page'), ['status' => 'Resolved'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'Resolved' ? 'bg-teal-600 text-white border-teal-600 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $status === 'Resolved' ? 'text-teal-100' : 'text-slate-400' }}">Resolved</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['resolved']) }}</span>
        </a>

        <!-- Closed -->
        <a href="{{ route('admin.welfare-cases.index', array_merge(request()->except('page'), ['status' => 'Closed'])) }}" 
           class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'Closed' ? 'bg-slate-700 text-white border-slate-700 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $status === 'Closed' ? 'text-slate-200' : 'text-slate-400' }}">Closed</span>
            <span class="text-xl font-extrabold mt-1">{{ number_format($stats['closed']) }}</span>
        </a>
    </div>

    <!-- Search & Filter Filter Form -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.welfare-cases.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by Case ID, Student Name, or Student ID..." 
                       class="block w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] focus:border-transparent outline-none transition">
            </div>

            <select name="category" 
                    class="w-full sm:w-64 py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none transition">
                <option value="">All Categories</option>
                <option value="Financial Assistance" {{ request('category') === 'Financial Assistance' ? 'selected' : '' }}>Financial Assistance</option>
                <option value="Guidance and Counseling" {{ request('category') === 'Guidance and Counseling' ? 'selected' : '' }}>Guidance and Counseling</option>
                <option value="Medical / Health Concern" {{ request('category') === 'Medical / Health Concern' ? 'selected' : '' }}>Medical / Health Concern</option>
                <option value="Academic Grievance" {{ request('category') === 'Academic Grievance' ? 'selected' : '' }}>Academic Grievance</option>
                <option value="Student Conduct / Discipline" {{ request('category') === 'Student Conduct / Discipline' ? 'selected' : '' }}>Student Conduct / Discipline</option>
                <option value="Housing / Dormitory" {{ request('category') === 'Housing / Dormitory' ? 'selected' : '' }}>Housing / Dormitory</option>
                <option value="Other" {{ request('category') === 'Other' ? 'selected' : '' }}>Other Welfare Concern</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-xs cursor-pointer shrink-0">
                Filter
            </button>

            @if(request('search') || request('category') || request('status'))
                <a href="{{ route('admin.welfare-cases.index') }}" class="w-full sm:w-auto px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition text-center shrink-0">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Welfare Cases Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Student</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Referral Info</th>
                        <th class="py-3.5 px-4">Date Reported</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($welfareCases as $case)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            

                            <!-- Student Info -->
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-[#3B060F] text-[#FFC107] font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($case->student->user->first_name ?? 'S', 0, 1) . substr($case->student->user->last_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-200">
                                            {{ $case->student->user->full_name ?? 'Unknown Student' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">
                                            ID: {{ $case->student->student_number ?? 'N/A' }} &bull; {{ $case->student->course ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                    {{ $case->category }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                @php
                                    $statusColors = [
                                        'Open' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                                        'Under Assessment' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
                                        'Referred' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                                        'For Follow-up' => 'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
                                        'Resolved' => 'bg-teal-50 text-teal-800 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
                                        'Closed' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                    ];
                                    $badgeClass = $statusColors[$case->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClass }}">
                                    {{ $case->status }}
                                </span>
                            </td>

                            <!-- Referral Info -->
                            <td class="py-4 px-4">
                                @if($case->referrals->count() > 0)
                                    @php $firstRef = $case->referrals->first(); @endphp
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $firstRef->recipient_name }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">None</span>
                                @endif
                            </td>

                            <!-- Date Reported -->
                            <td class="py-4 px-4 text-slate-500 dark:text-slate-400 text-[11px] font-medium">
                                {{ $case->created_at->format('M d, Y') }}
                                <span class="block text-[10px] text-slate-400">{{ $case->created_at->format('h:i A') }}</span>
                            </td>

                            <!-- Action -->
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <a href="{{ route('admin.welfare-cases.show', $case->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#3B060F] text-white font-extrabold text-[11px] rounded-xl hover:bg-[#6B0F1A] transition shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View & Assess
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                No welfare cases found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($welfareCases->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                {{ $welfareCases->links() }}
            </div>
        @endif
    </div>

</x-app-layout>

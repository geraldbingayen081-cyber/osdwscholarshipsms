<x-app-layout title="Reports & Analytics - CSU Lal-lo Admin">

    <!-- Print Header Styling -->
    <style>
        @media print {
            aside, header, footer, .no-print { display: none !important; }
            body { background: white !important; }
            .print-container { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>

    <div class="print-container">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Reports & Analytics</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    OSDW Scholarship Program Performance, Slot Utilization, and Student Distribution Statistics.
                </p>
            </div>

            <!-- Academic Year Filter & Print Button -->
            <div class="no-print flex items-center space-x-3">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center space-x-2">
                    <select name="academic_year_id" onchange="this.form.submit()" class="py-2 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $selectedAY && $selectedAY->id == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }} {{ $ay->status === 'active' ? '(Active)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!--<a href="{{ route('admin.export.scholars', request()->all()) }}" 
                   class="px-4 py-2 bg-[#6B0F1A] hover:bg-[#500A15] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>

                <button onclick="window.print()" class="px-4 py-2 bg-[#3B060F] text-white font-bold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-xs flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>-->
            </div>
        </div>

        <!-- Metric Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Apps -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500">Total Applications</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalApplications) }}</h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">{{ $selectedAY ? $selectedAY->name : 'All-time' }}</p>
            </div>

            <!-- Acceptance Rate -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500">Acceptance Rate</p>
                <h3 class="text-2xl font-extrabold text-[#3B060F] mt-1">{{ $acceptanceRate }}%</h3>
                <p class="text-[11px] font-bold text-emerald-600 mt-1">{{ $approvedCount }} Approved</p>
            </div>

            <!-- Pending Apps -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500">Pending Review</p>
                <h3 class="text-2xl font-extrabold text-amber-700 mt-1">{{ number_format($pendingCount) }}</h3>
                <p class="text-[11px] font-bold text-amber-600 mt-1">Awaiting decision</p>
            </div>

            <!-- Incomplete Apps -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500">Incomplete Documents</p>
                <h3 class="text-2xl font-extrabold text-rose-700 mt-1">{{ number_format($incompleteCount) }}</h3>
                <p class="text-[11px] font-bold text-rose-600 mt-1">Resubmission requested</p>
            </div>

            <!-- Rejected Apps -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500">Rejected Applications</p>
                <h3 class="text-2xl font-extrabold text-slate-600 mt-1">{{ number_format($rejectedCount) }}</h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">Not qualified</p>
            </div>
        </div>

        <!-- 2-COLUMN SECTION: Slot Utilization & Course Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Scholarship Program Slot Utilization -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center justify-between">
                    <span>Program Slot Utilization</span>
                    <span class="text-xs font-normal text-slate-400">Grantees vs Available</span>
                </h3>

                <div class="space-y-4">
                    @forelse($scholarships as $sch)
                        @php
                            $utilizedPercent = $sch->available_slots > 0 
                                ? min(100, round(($sch->scholars_count / $sch->available_slots) * 100))
                                : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold mb-1">
                                <span class="text-slate-900">{{ $sch->name }}</span>
                                <span class="text-slate-600 font-mono">
                                    {{ $sch->scholars_count }} / {{ $sch->available_slots }} Slots ({{ $utilizedPercent }}%)
                                </span>
                            </div>

                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#3B060F] transition-all duration-500 rounded-full" 
                                     style="width: {{ $utilizedPercent }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            No scholarship programs created yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Student Course Breakdown Table -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                    Demographics Breakdown by Course / Program
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2.5">Course / Degree Program</th>
                                <th class="px-4 py-2.5 text-right">Registered Students</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($courseBreakdown as $c)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->course }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-900 text-right font-mono">{{ number_format($c->total_students) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-slate-400 text-xs">
                                        No student data found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Recent Active Scholars Roster Table -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                Recently Enrolled Active Scholars Roster
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Student No.</th>
                            <th class="px-6 py-3">Scholarship Program</th>
                            <th class="px-6 py-3">Approved Date</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentScholars as $sch)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3.5 font-bold text-slate-900">
                                    {{ $sch->student->user->full_name }}
                                </td>
                                <td class="px-6 py-3.5 font-mono text-slate-600">
                                    {{ $sch->student->student_number }}
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-800">
                                    {{ $sch->scholarship->name }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $sch->approved_at ? $sch->approved_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        {{ str_replace('_', ' ', $sch->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-slate-400 text-xs">
                                    No active scholars recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>

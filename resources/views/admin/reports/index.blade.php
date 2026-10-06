<x-app-layout title="Reports - CSU Lal-lo Admin">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Reports</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    Official OSDW Scholarship rosters, slot allocations, student welfare case summaries, and printable reports.
                </p>
            </div>

            <!-- Tab Switcher -->
            <div class="inline-flex p-1 bg-slate-200/80 rounded-2xl">
                <a href="{{ route('admin.reports.index', ['tab' => 'scholarships']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 {{ $tab === 'scholarships' ? 'bg-[#3B060F] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                    Scholarship Reports
                </a>
                <a href="{{ route('admin.reports.index', ['tab' => 'welfare']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 {{ $tab === 'welfare' ? 'bg-[#3B060F] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Student Welfare Reports
                </a>
            </div>
        </div>

        <!-- Filter Configuration Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Report Type Selection -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                            Report Type
                        </label>
                        <select name="type" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                            @if($tab === 'scholarships')
                                <option value="scholars_masterlist" {{ $type === 'scholars_masterlist' ? 'selected' : '' }}>
                                     Masterlist of Active Scholars
                                </option>
                                <option value="applications_summary" {{ $type === 'applications_summary' ? 'selected' : '' }}>
                                     Applications & Evaluation Summary
                                </option>
                                <option value="slot_utilization" {{ $type === 'slot_utilization' ? 'selected' : '' }}>
                                     Program Slot Utilization & Capacity
                                </option>
                                <option value="compliance_tracking" {{ $type === 'compliance_tracking' ? 'selected' : '' }}>
                                     Scholar Compliance & Renewal Tracking
                                </option>
                            @else
                                <option value="welfare_masterlist" {{ $type === 'welfare_masterlist' ? 'selected' : '' }}>
                                     Welfare Cases Master Intake Log
                                </option>
                                <option value="welfare_referrals" {{ $type === 'welfare_referrals' ? 'selected' : '' }}>
                                     Welfare Referrals & Endorsements
                                </option>
                            @endif
                        </select>
                    </div>

                    @if($tab === 'scholarships')
                        <!-- Scholarship Program Filter -->
                        @if(in_array($type, ['scholars_masterlist', 'applications_summary', 'compliance_tracking']))
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Scholarship Program
                                </label>
                                <select name="scholarship_id" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                    <option value="">All Scholarship Programs</option>
                                    @foreach($scholarships as $sch)
                                        <option value="{{ $sch->id }}" {{ $scholarshipId == $sch->id ? 'selected' : '' }}>
                                            {{ $sch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Academic Year Filter -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Academic Year
                            </label>
                            <select name="academic_year_id" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                <option value="">All Academic Years</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>
                                        {{ $ay->name }} {{ $ay->status === 'active' ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- College Filter -->
                        @if(in_array($type, ['scholars_masterlist', 'applications_summary']))
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    College / Department
                                </label>
                                <select name="college" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                    <option value="">All Colleges</option>
                                    @foreach($colleges as $key => $name)
                                        <option value="{{ $key }}" {{ $college == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                    @else
                        <!-- Welfare Tab Filters -->
                        @if($type === 'welfare_masterlist')
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Case Category
                                </label>
                                <select name="category" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                    <option value="">All Categories</option>
                                    <option value="Financial Assistance" {{ $category === 'Financial Assistance' ? 'selected' : '' }}>Financial Assistance</option>
                                    <option value="Academic Hardship" {{ $category === 'Academic Hardship' ? 'selected' : '' }}>Academic Hardship</option>
                                    <option value="Health / Medical Concern" {{ $category === 'Health / Medical Concern' ? 'selected' : '' }}>Health / Medical Concern</option>
                                    <option value="Family / Personal Crisis" {{ $category === 'Family / Personal Crisis' ? 'selected' : '' }}>Family / Personal Crisis</option>
                                    <option value="Emergency Relief" {{ $category === 'Emergency Relief' ? 'selected' : '' }}>Emergency Relief</option>
                                    <option value="Other" {{ $category === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Case Status Filter -->
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Case Status
                                </label>
                                <select name="status" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                    <option value="">All Statuses</option>
                                    <option value="Open" {{ $status === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Under Assessment" {{ $status === 'Under Assessment' ? 'selected' : '' }}>Under Assessment</option>
                                    <option value="Referred" {{ $status === 'Referred' ? 'selected' : '' }}>Referred</option>
                                    <option value="For Follow-up" {{ $status === 'For Follow-up' ? 'selected' : '' }}>For Follow-up</option>
                                    <option value="Resolved" {{ $status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="Closed" {{ $status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        @endif

                        <!-- Date Range Filters -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Date From
                            </label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Date To
                            </label>
                            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>
                    @endif

                </div>

                <!-- Form Bottom Actions -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter Results
                        </button>

                        <a href="{{ route('admin.reports.index', ['tab' => $tab]) }}" class="px-3.5 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            Reset Filters
                        </a>
                    </div>

                    <!-- Direct Export & Print Actions -->
                    <div class="flex items-center space-x-2">
                        @php
                            $exportRoute = match($type) {
                                'scholars_masterlist' => route('admin.export.scholars', request()->all()),
                                'applications_summary' => route('admin.export.applications', request()->all()),
                                'slot_utilization' => route('admin.export.slot_utilization', request()->all()),
                                'compliance_tracking' => route('admin.export.compliance', request()->all()),
                                'welfare_masterlist' => route('admin.export.welfare_cases', request()->all()),
                                'welfare_referrals' => route('admin.export.welfare_referrals', request()->all()),
                                default => route('admin.export.scholars', request()->all()),
                            };
                        @endphp

                        <a href="{{ route('admin.reports.print', request()->all()) }}" 
                           target="_blank"
                           class="px-4 py-2 bg-[#3B060F] hover:bg-[#500A15] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print / Download PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Metric Cards Row -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($summaryStats as $stat)
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $stat['label'] }}</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1 font-mono">{{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}</h3>
                </div>
            @endforeach
        </div>

        <!-- Report Table Preview Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                        {{ $reportTitle }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $reportSubtitle }}</p>
                </div>
                <div class="text-xs font-semibold text-slate-400">
                    Showing <span class="font-bold text-slate-800 font-mono">{{ $records->count() }}</span> record(s)
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    @if($tab === 'scholarships')
                        @if($type === 'scholars_masterlist')
                            <thead class="bg-slate-50 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3 w-12 text-center">No.</th>
                                    <th class="px-5 py-3">Student ID</th>
                                    <th class="px-5 py-3">Full Name</th>
                                    <th class="px-5 py-3 text-center">Sex</th>
                                    <th class="px-5 py-3">Course</th>
                                    <th class="px-5 py-3">Scholarship</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $index => $row)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-5 py-3.5 text-center font-bold text-slate-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3.5 font-mono font-bold text-slate-900">{{ $row->student->student_number ?? 'N/A' }}</td>
                                        <td class="px-5 py-3.5 font-bold text-slate-900">
                                            {{ $row->student->user->last_name ?? '' }}, {{ $row->student->user->first_name ?? '' }} {{ $row->student->user->middle_name ? substr($row->student->user->middle_name, 0, 1) . '.' : '' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-semibold uppercase text-slate-700">{{ $row->student->sex ?? 'N/A' }}</td>
                                        <td class="px-5 py-3.5 text-slate-800">{{ $row->student->course ?? 'N/A' }}</td>
                                        <td class="px-5 py-3.5 font-semibold text-slate-900">{{ $row->scholarship->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs">
                                            No scholars found matching the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        @elseif($type === 'applications_summary')
                            <thead class="bg-slate-50 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">No.</th>
                                    <th class="px-4 py-3">Student ID</th>
                                    <th class="px-4 py-3">Applicant Name</th>
                                    <th class="px-4 py-3 text-center">Sex</th>
                                    <th class="px-4 py-3">Course</th>
                                    <th class="px-4 py-3">Target Scholarship</th>
                                    <th class="px-4 py-3 text-center">GWA</th>
                                    <th class="px-4 py-3">Monthly Income</th>
                                    <th class="px-4 py-3 text-center">4Ps</th>
                                    <th class="px-4 py-3">Date Filed</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $index => $row)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5 font-mono font-bold text-slate-900">{{ $row->student->student_number ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            {{ $row->student->user->last_name ?? '' }}, {{ $row->student->user->first_name ?? '' }} {{ $row->student->user->middle_name ? substr($row->student->user->middle_name, 0, 1) . '.' : '' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-semibold uppercase text-slate-700">{{ $row->student->sex ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5">{{ $row->student->course ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-semibold text-slate-900">{{ $row->scholarship->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 text-center font-mono font-bold text-slate-800">{{ $row->student->current_gwa ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-mono">{{ $row->student->monthly_household_income ? '₱' . number_format($row->student->monthly_household_income, 2) : 'N/A' }}</td>
                                        <td class="px-4 py-3.5 text-center">
                                            @if($row->student->is_4ps)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">4Ps</span>
                                            @else
                                                <span class="text-slate-400">No</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-500">{{ $row->submitted_at ? $row->submitted_at->format('M d, Y') : 'N/A' }}</td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $row->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ in_array($row->status, ['submitted', 'under_review']) ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $row->status === 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                                {{ $row->status === 'incomplete' ? 'bg-slate-100 text-slate-800' : '' }}">
                                                {{ str_replace('_', ' ', $row->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-6 py-12 text-center text-slate-400 text-xs">
                                            No applications found matching the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        @elseif($type === 'slot_utilization')
                            <thead class="bg-slate-50 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3 w-12 text-center">No.</th>
                                    <th class="px-5 py-3">Scholarship Program</th>
                                    <th class="px-5 py-3">Provider / Sponsor</th>
                                    <th class="px-5 py-3">Coverage Type</th>
                                    <th class="px-5 py-3 text-center">Allocated</th>
                                    <th class="px-5 py-3 text-center">Applicants</th>
                                    <th class="px-5 py-3 text-center">Enrolled</th>
                                    <th class="px-5 py-3 text-center">Remaining</th>
                                    <th class="px-5 py-3 text-center">Utilization</th>
                                    <th class="px-5 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $index => $row)
                                    @php
                                        $rem = max(0, $row->available_slots - $row->scholars_count);
                                        $rate = $row->available_slots > 0 ? round(($row->scholars_count / $row->available_slots) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-5 py-3.5 text-center font-bold text-slate-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3.5 font-bold text-slate-900">{{ $row->name }}</td>
                                        <td class="px-5 py-3.5">{{ $row->provider ?? 'N/A' }}</td>
                                        <td class="px-5 py-3.5">{{ $row->coverage_type_label ?? 'N/A' }}</td>
                                        <td class="px-5 py-3.5 text-center font-mono font-bold text-slate-900">{{ $row->available_slots }}</td>
                                        <td class="px-5 py-3.5 text-center font-mono text-slate-600">{{ $row->applications_count }}</td>
                                        <td class="px-5 py-3.5 text-center font-mono font-bold text-emerald-700">{{ $row->scholars_count }}</td>
                                        <td class="px-5 py-3.5 text-center font-mono text-slate-600">{{ $rem }}</td>
                                        <td class="px-5 py-3.5 text-center font-mono font-bold">
                                            <span class="px-2 py-0.5 rounded-md text-[11px] {{ $rate >= 100 ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-800' }}">
                                                {{ $rate }}%
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $row->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $row->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-12 text-center text-slate-400 text-xs">
                                            No scholarship programs recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        @elseif($type === 'compliance_tracking')
                            <thead class="bg-slate-50 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">No.</th>
                                    <th class="px-4 py-3">Student ID</th>
                                    <th class="px-4 py-3">Scholar Name</th>
                                    <th class="px-4 py-3 text-center">Sex</th>
                                    <th class="px-4 py-3">Course</th>
                                    <th class="px-4 py-3">Scholarship</th>
                                    <th class="px-4 py-3">Compliance Request</th>
                                    <th class="px-4 py-3">Submitted Date</th>
                                    <th class="px-4 py-3 text-center">GWA</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $index => $row)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5 font-mono font-bold text-slate-900">{{ $row->scholar->student->student_number ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            {{ $row->scholar->student->user->last_name ?? '' }}, {{ $row->scholar->student->user->first_name ?? '' }} {{ $row->scholar->student->user->middle_name ? substr($row->scholar->student->user->middle_name, 0, 1) . '.' : '' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-semibold uppercase text-slate-700">{{ $row->scholar->student->sex ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5">{{ $row->scholar->student->course ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-semibold text-slate-900">{{ $row->scholar->scholarship->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5">{{ $row->complianceRequest->title ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 text-slate-500">{{ $row->submitted_at ? $row->submitted_at->format('M d, Y') : 'Pending' }}</td>
                                        <td class="px-4 py-3.5 text-center font-mono font-bold">{{ $row->scholar->student->current_gwa ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $row->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ in_array($row->status, ['submitted', 'under_review']) ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $row->status === 'overdue' ? 'bg-rose-100 text-rose-800' : '' }}
                                                {{ in_array($row->status, ['not_submitted', 'partially_submitted']) ? 'bg-slate-100 text-slate-700' : '' }}">
                                                {{ $row->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-12 text-center text-slate-400 text-xs">
                                            No compliance records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        @endif

                    @else
                        {{-- Welfare Tab Tables --}}
                        @if($type === 'welfare_masterlist')
                            <thead class="bg-slate-50 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">No.</th>
                                    <th class="px-4 py-3">Intake Date</th>
                                    <th class="px-4 py-3">Student ID</th>
                                    <th class="px-4 py-3">Student Name</th>
                                    <th class="px-4 py-3 text-center">Sex</th>
                                    <th class="px-4 py-3">Course</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $index => $row)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5 text-slate-500">{{ $row->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-3.5 font-mono">{{ $row->student->student_number ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            {{ $row->student->user->last_name ?? '' }}, {{ $row->student->user->first_name ?? '' }} {{ $row->student->user->middle_name ? substr($row->student->user->middle_name, 0, 1) . '.' : '' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-semibold uppercase text-slate-700">{{ $row->student->sex ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5">{{ $row->student->course ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-semibold text-slate-800">{{ $row->category }}</td>
                                        <td class="px-4 py-3.5 text-slate-600 max-w-xs truncate" title="{{ $row->description }}">{{ $row->description }}</td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ in_array($row->status, ['Resolved', 'Closed']) ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ in_array($row->status, ['Open', 'Under Assessment']) ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $row->status === 'Referred' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $row->status === 'For Follow-up' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ $row->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-6 py-12 text-center text-slate-400 text-xs">
                                            No welfare cases found matching the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        @elseif($type === 'welfare_referrals')
                            <thead class="bg-slate-50 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">No.</th>
                                    <th class="px-4 py-3">Student Name</th>
                                    <th class="px-4 py-3 text-center">Sex</th>
                                    <th class="px-4 py-3">Course</th>
                                    <th class="px-4 py-3">Referral Type</th>
                                    <th class="px-4 py-3">Referred To</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Endorsement Reason</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $index => $row)
                                    @php
                                        $dest = $row->referral_type === 'scholarship' 
                                            ? ($row->scholarship->name ?? 'Scholarship Program') 
                                            : ($row->referred_to_office ?? 'Campus Office');
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            {{ $row->welfareCase->student->user->last_name ?? '' }}, {{ $row->welfareCase->student->user->first_name ?? '' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-semibold uppercase text-slate-700">{{ $row->welfareCase->student->sex ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5">{{ $row->welfareCase->student->course ?? 'N/A' }}</td>
                                        <td class="px-4 py-3.5 font-semibold uppercase text-slate-700">{{ str_replace('_', ' ', $row->referral_type) }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-900">{{ $dest }}</td>
                                        <td class="px-4 py-3.5 text-slate-500">{{ $row->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-3.5 text-slate-600 max-w-xs truncate" title="{{ $row->referral_note }}">{{ $row->referral_note ?? 'None' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center text-slate-400 text-xs">
                                            No referrals found matching the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        @endif
                    @endif
                </table>
            </div>
        </div>

    </div>

</x-app-layout>

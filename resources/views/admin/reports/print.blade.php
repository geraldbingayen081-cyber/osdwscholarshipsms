<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reportTitle }} - CSU Lal-lo OSDW</title>

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: auto;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif, system-ui;
            background-color: #ffffff;
            color: #000000;
        }

        .report-table th, .report-table td {
            border: 1px solid #1e293b;
            padding: 6px 8px;
            font-size: 11px;
            line-height: 1.25;
        }

        .report-table th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8 max-w-[1100px] mx-auto text-slate-900">

    <!-- Top Floating Toolbar (Hidden when printed) -->
    <div class="no-print mb-6 p-4 bg-slate-800 text-white rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-2">
            <span class="inline-flex h-3 w-3 rounded-full bg-emerald-400"></span>
            <span class="text-xs font-bold font-sans">Official Report Print Preview</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#FFC107] text-[#3B060F] font-extrabold text-xs rounded-xl hover:bg-amber-400 transition flex items-center gap-1.5 shadow-md font-sans">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Document / Save as PDF
            </button>
            <button onclick="window.close()" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs rounded-xl transition font-sans">
                Close Preview
            </button>
        </div>
    </div>

    <!-- Official CSU Letterhead Header -->
    <div class="border-b-2 border-slate-900 pb-4 mb-5 flex items-center justify-between gap-4 text-center">
        <div class="w-20 shrink-0 flex justify-start">
            @if(\App\Models\SystemSetting::schoolLogoUrl())
                <img src="{{ \App\Models\SystemSetting::schoolLogoUrl() }}" alt="School Logo" class="h-20 w-20 object-contain">
            @else
                <x-csu-logo class="h-20 w-20" />
            @endif
        </div>

        <div class="flex-1">
            <p class="text-xs uppercase tracking-widest font-serif text-slate-700">Republic of the Philippines</p>
            <h1 class="text-lg sm:text-xl font-bold font-serif text-slate-950 uppercase tracking-tight leading-tight">
                {{ $institutionName }}
            </h1>
            <p class="text-xs font-bold font-serif text-slate-800 uppercase tracking-wider">
                {{ $campusName }}
            </p>
            <p class="text-xs font-bold text-[#6B0F1A] uppercase tracking-wider mt-0.5">
                {{ $officeName }}
            </p>
        </div>

        <div class="w-20 shrink-0 flex justify-end">
            @if(\App\Models\SystemSetting::logoUrl())
                <img src="{{ \App\Models\SystemSetting::logoUrl() }}" alt="System Logo" class="h-20 w-20 object-contain">
            @else
                <x-csu-logo class="h-20 w-20 opacity-80" />
            @endif
        </div>
    </div>

    <!-- Document Title & Meta Information -->
    <div class="text-center mb-6">
        <h2 class="text-base sm:text-lg font-extrabold uppercase tracking-wide text-slate-950 font-serif">
            {{ $reportTitle }}
        </h2>
        <p class="text-xs font-serif text-slate-700 mt-0.5">
            {{ $reportSubtitle }}
        </p>
        <p class="text-[10px] font-sans text-slate-500 mt-1">
            Date Generated: {{ now()->format('F d, Y - h:i A') }}
        </p>
    </div>

    <!-- Report Table Data -->
    <div class="overflow-x-auto mb-8">
        <table class="w-full report-table text-left border-collapse">
            @if($tab === 'scholarships')
                @if($type === 'scholars_masterlist')
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No.</th>
                            <th class="w-28">Student ID</th>
                            <th>Full Name</th>
                            <th class="w-16 text-center">Sex</th>
                            <th>Course</th>
                            <th>Scholarship</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $row)
                            <tr class="{{ $index % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="text-center font-bold">{{ $index + 1 }}</td>
                                <td class="font-mono text-slate-900">{{ $row->student->student_number ?? 'N/A' }}</td>
                                <td class="font-bold text-slate-900 uppercase">
                                    {{ $row->student->user->last_name ?? '' }}, {{ $row->student->user->first_name ?? '' }} {{ $row->student->user->middle_name ? substr($row->student->user->middle_name, 0, 1) . '.' : '' }}
                                </td>
                                <td class="text-center uppercase">{{ $row->student->sex ?? 'N/A' }}</td>
                                <td>{{ $row->student->course ?? 'N/A' }}</td>
                                <td class="font-semibold text-slate-900">{{ $row->scholarship->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-400 italic">No scholars found matching the selected criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($type === 'applications_summary')
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No.</th>
                            <th>Student ID</th>
                            <th>Applicant Name</th>
                            <th class="text-center">Sex</th>
                            <th>Course</th>
                            <th>Target Scholarship</th>
                            <th class="text-center">GWA</th>
                            <th>Monthly Income</th>
                            <th class="text-center">4Ps</th>
                            <th>Date Filed</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $row)
                            <tr class="{{ $index % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="text-center font-bold">{{ $index + 1 }}</td>
                                <td class="font-mono">{{ $row->student->student_number ?? 'N/A' }}</td>
                                <td class="font-bold uppercase">
                                    {{ $row->student->user->last_name ?? '' }}, {{ $row->student->user->first_name ?? '' }} {{ $row->student->user->middle_name ? substr($row->student->user->middle_name, 0, 1) . '.' : '' }}
                                </td>
                                <td class="text-center uppercase">{{ $row->student->sex ?? 'N/A' }}</td>
                                <td>{{ $row->student->course ?? 'N/A' }}</td>
                                <td class="font-semibold">{{ $row->scholarship->name ?? 'N/A' }}</td>
                                <td class="text-center font-mono font-bold">{{ $row->student->current_gwa ?? 'N/A' }}</td>
                                <td>{{ $row->student->monthly_household_income ? '₱' . number_format($row->student->monthly_household_income, 2) : 'N/A' }}</td>
                                <td class="text-center">{{ $row->student->is_4ps ? 'Yes' : 'No' }}</td>
                                <td>{{ $row->submitted_at ? $row->submitted_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="font-bold uppercase">{{ str_replace('_', ' ', $row->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-6 text-slate-400 italic">No applications found matching the selected criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($type === 'slot_utilization')
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No.</th>
                            <th>Scholarship Program</th>
                            <th>Provider / Sponsor</th>
                            <th>Coverage Type</th>
                            <th class="text-center">Allocated</th>
                            <th class="text-center">Applicants</th>
                            <th class="text-center">Enrolled</th>
                            <th class="text-center">Remaining</th>
                            <th class="text-center">Utilization</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $row)
                            @php
                                $rem = max(0, $row->available_slots - $row->scholars_count);
                                $rate = $row->available_slots > 0 ? round(($row->scholars_count / $row->available_slots) * 100, 1) : 0;
                            @endphp
                            <tr class="{{ $index % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="text-center font-bold">{{ $index + 1 }}</td>
                                <td class="font-bold">{{ $row->name }}</td>
                                <td>{{ $row->provider ?? 'N/A' }}</td>
                                <td>{{ $row->coverage_type_label ?? 'N/A' }}</td>
                                <td class="text-center font-mono font-bold">{{ $row->available_slots }}</td>
                                <td class="text-center font-mono">{{ $row->applications_count }}</td>
                                <td class="text-center font-mono font-bold text-emerald-800">{{ $row->scholars_count }}</td>
                                <td class="text-center font-mono">{{ $rem }}</td>
                                <td class="text-center font-mono font-bold">{{ $rate }}%</td>
                                <td class="uppercase text-xs font-semibold">{{ $row->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-6 text-slate-400 italic">No scholarship programs recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($type === 'compliance_tracking')
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No.</th>
                            <th>Student ID</th>
                            <th>Scholar Name</th>
                            <th class="text-center">Sex</th>
                            <th>Course</th>
                            <th>Scholarship</th>
                            <th>Compliance Request</th>
                            <th>Submitted Date</th>
                            <th class="text-center">GWA</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $row)
                            <tr class="{{ $index % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="text-center font-bold">{{ $index + 1 }}</td>
                                <td class="font-mono">{{ $row->scholar->student->student_number ?? 'N/A' }}</td>
                                <td class="font-bold uppercase">
                                    {{ $row->scholar->student->user->last_name ?? '' }}, {{ $row->scholar->student->user->first_name ?? '' }} {{ $row->scholar->student->user->middle_name ? substr($row->scholar->student->user->middle_name, 0, 1) . '.' : '' }}
                                </td>
                                <td class="text-center uppercase">{{ $row->scholar->student->sex ?? 'N/A' }}</td>
                                <td>{{ $row->scholar->student->course ?? 'N/A' }}</td>
                                <td class="font-semibold">{{ $row->scholar->scholarship->name ?? 'N/A' }}</td>
                                <td>{{ $row->complianceRequest->title ?? 'N/A' }}</td>
                                <td>{{ $row->submitted_at ? $row->submitted_at->format('M d, Y') : 'Pending' }}</td>
                                <td class="text-center font-mono font-bold">{{ $row->scholar->student->current_gwa ?? 'N/A' }}</td>
                                <td class="font-bold uppercase">{{ $row->status_label }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-6 text-slate-400 italic">No compliance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                @endif

            @else
                {{-- Welfare Reports --}}
                @if($type === 'welfare_masterlist')
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No.</th>
                            <th class="w-24">Case ID</th>
                            <th>Intake Date</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th class="text-center">Sex</th>
                            <th>Course</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $row)
                            <tr class="{{ $index % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="text-center font-bold">{{ $index + 1 }}</td>
                                <td class="font-mono font-bold">{{ $row->case_id }}</td>
                                <td>{{ $row->created_at->format('M d, Y') }}</td>
                                <td class="font-mono">{{ $row->student->student_number ?? 'N/A' }}</td>
                                <td class="font-bold uppercase">
                                    {{ $row->student->user->last_name ?? '' }}, {{ $row->student->user->first_name ?? '' }} {{ $row->student->user->middle_name ? substr($row->student->user->middle_name, 0, 1) . '.' : '' }}
                                </td>
                                <td class="text-center uppercase">{{ $row->student->sex ?? 'N/A' }}</td>
                                <td>{{ $row->student->course ?? 'N/A' }}</td>
                                <td class="font-semibold">{{ $row->category }}</td>
                                <td>{{ Str::limit($row->description, 60) }}</td>
                                <td class="font-bold uppercase">{{ $row->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-6 text-slate-400 italic">No student welfare cases found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($type === 'welfare_referrals')
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No.</th>
                            <th>Case ID</th>
                            <th>Student Name</th>
                            <th class="text-center">Sex</th>
                            <th>Course</th>
                            <th>Referral Type</th>
                            <th>Referred To</th>
                            <th>Date</th>
                            <th>Endorsement Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $row)
                            @php
                                $dest = $row->referral_type === 'scholarship' 
                                    ? ($row->scholarship->name ?? 'Scholarship Program') 
                                    : ($row->referred_to_office ?? 'Campus Office');
                            @endphp
                            <tr class="{{ $index % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="text-center font-bold">{{ $index + 1 }}</td>
                                <td class="font-mono font-bold">{{ $row->welfareCase->case_id ?? 'N/A' }}</td>
                                <td class="font-bold uppercase">
                                    {{ $row->welfareCase->student->user->last_name ?? '' }}, {{ $row->welfareCase->student->user->first_name ?? '' }}
                                </td>
                                <td class="text-center uppercase">{{ $row->welfareCase->student->sex ?? 'N/A' }}</td>
                                <td>{{ $row->welfareCase->student->course ?? 'N/A' }}</td>
                                <td class="uppercase font-semibold">{{ str_replace('_', ' ', $row->referral_type) }}</td>
                                <td class="font-bold text-slate-900">{{ $dest }}</td>
                                <td>{{ $row->created_at->format('M d, Y') }}</td>
                                <td>{{ Str::limit($row->referral_note ?? 'N/A', 60) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-6 text-slate-400 italic">No referrals found matching the selected criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                @endif
            @endif
        </table>
    </div>

    <!-- Summary Count Footnote -->
    <div class="mb-12 flex items-center justify-between text-xs font-serif text-slate-700">
        <div>
            Total Records Listed: <span class="font-bold">{{ $records->count() }}</span>
        </div>
        <div class="text-right text-[11px] italic">
            *** Nothing Follows ***
        </div>
    </div>

    <!-- Official Signatory Block -->
    <div class="grid grid-cols-2 gap-12 pt-8 text-slate-950 font-serif">
        <div>
            <p class="text-xs mb-10">Prepared by:</p>
            <div class="w-64 border-b border-slate-900 mb-1.5"></div>
            <p class="font-bold text-xs uppercase tracking-wider">OSDW Coordinator</p>
            <p class="text-[11px] text-slate-600">Office of Student Development and Welfare</p>
        </div>

        <div>
            <p class="text-xs mb-10">Approved by:</p>
            <div class="w-64 border-b border-slate-900 mb-1.5"></div>
            <p class="font-bold text-xs uppercase tracking-wider">Campus Executive Officer / Dean</p>
            <p class="text-[11px] text-slate-600">CSU Lal-lo Campus</p>
        </div>
    </div>

</body>
</html>

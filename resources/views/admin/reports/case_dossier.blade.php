<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Welfare Case Dossier - {{ $case->case_id }}</title>

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif, system-ui;
            background-color: #ffffff;
            color: #000000;
        }

        .border-box {
            border: 1px solid #1e293b;
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
        }
    </style>
</head>
<body class="p-4 sm:p-8 max-w-[900px] mx-auto text-slate-900">

    <!-- Top Floating Toolbar -->
    <div class="no-print mb-6 p-4 bg-slate-800 text-white rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-2">
            <span class="inline-flex h-3 w-3 rounded-full bg-emerald-400"></span>
            <span class="text-xs font-bold font-sans">Individual Case Dossier Print Preview</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#FFC107] text-[#3B060F] font-extrabold text-xs rounded-xl hover:bg-amber-400 transition flex items-center gap-1.5 shadow-md font-sans">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print / Save Case Dossier (PDF)
            </button>
            <button onclick="window.close()" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs rounded-xl transition font-sans">
                Close Preview
            </button>
        </div>
    </div>

    <!-- Official Header -->
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

    <!-- Document Heading -->
    <div class="text-center mb-6">
        <h2 class="text-base sm:text-lg font-extrabold uppercase tracking-wide text-slate-950 font-serif">
            STUDENT WELFARE CASE INTAKE & EVALUATION DOSSIER
        </h2>
        <p class="text-xs font-serif text-slate-700 mt-0.5">
            Case Control ID: <span class="font-mono font-bold">{{ $case->case_id }}</span> | Intake Date: <span class="font-bold">{{ $case->created_at->format('F d, Y') }}</span>
        </p>
    </div>

    <!-- Section 1: Student Background Profile -->
    <div class="mb-5">
        <h3 class="text-xs font-bold uppercase tracking-wider bg-slate-100 border border-slate-900 px-3 py-1 text-slate-900 font-sans">
            I. Student Personal & Academic Profile
        </h3>
        <table class="w-full text-xs border border-t-0 border-slate-900">
            <tr>
                <td class="p-2 w-1/4 font-bold border-r border-b border-slate-900 bg-slate-50">Student Full Name:</td>
                <td class="p-2 w-1/4 border-r border-b border-slate-900 uppercase font-bold">{{ $case->student->user->last_name }}, {{ $case->student->user->first_name }} {{ $case->student->user->middle_name }}</td>
                <td class="p-2 w-1/4 font-bold border-r border-b border-slate-900 bg-slate-50">Student ID Number:</td>
                <td class="p-2 w-1/4 border-b border-slate-900 font-mono">{{ $case->student->student_number }}</td>
            </tr>
            <tr>
                <td class="p-2 font-bold border-r border-b border-slate-900 bg-slate-50">Sex / Gender:</td>
                <td class="p-2 border-r border-b border-slate-900 uppercase">{{ $case->student->sex ?? 'N/A' }}</td>
                <td class="p-2 font-bold border-r border-b border-slate-900 bg-slate-50">Contact Number:</td>
                <td class="p-2 border-b border-slate-900 font-mono">{{ $case->student->contact_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="p-2 font-bold border-r border-b border-slate-900 bg-slate-50">College & Course:</td>
                <td class="p-2 border-r border-b border-slate-900">{{ $case->student->college }} - {{ $case->student->course }}</td>
                <td class="p-2 font-bold border-r border-b border-slate-900 bg-slate-50">Year Level:</td>
                <td class="p-2 border-b border-slate-900">{{ $case->student->year_level }}</td>
            </tr>
            <tr>
                <td class="p-2 font-bold border-r border-b border-slate-900 bg-slate-50">Monthly Household Income:</td>
                <td class="p-2 border-r border-b border-slate-900">{{ $case->student->monthly_household_income ? '₱' . number_format($case->student->monthly_household_income, 2) : 'N/A' }}</td>
                <td class="p-2 font-bold border-r border-b border-slate-900 bg-slate-50">4Ps Beneficiary:</td>
                <td class="p-2 border-b border-slate-900">{{ $case->student->is_4ps ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td class="p-2 font-bold border-r border-slate-900 bg-slate-50">Residential Address:</td>
                <td colspan="3" class="p-2">{{ $case->student->barangay ?? '' }} {{ $case->student->municipality ? ', ' . $case->student->municipality : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section 2: Case Details & Assessment -->
    <div class="mb-5">
        <h3 class="text-xs font-bold uppercase tracking-wider bg-slate-100 border border-slate-900 px-3 py-1 text-slate-900 font-sans">
            II. Welfare Case Assessment & Statement of Concern
        </h3>
        <table class="w-full text-xs border border-t-0 border-slate-900">
            <tr>
                <td class="p-2 w-1/4 font-bold border-r border-b border-slate-900 bg-slate-50">Case Category:</td>
                <td class="p-2 w-1/4 border-r border-b border-slate-900 font-bold">{{ $case->category }}</td>
                <td class="p-2 w-1/4 font-bold border-r border-b border-slate-900 bg-slate-50">Current Status:</td>
                <td class="p-2 w-1/4 border-b border-slate-900 font-bold uppercase">{{ $case->status }}</td>
            </tr>
            <tr>
                <td class="p-2 font-bold border-r border-slate-900 bg-slate-50 align-top">Statement / Need:</td>
                <td colspan="3" class="p-3 text-slate-900 leading-relaxed min-h-[70px]">
                    {{ $case->description }}
                </td>
            </tr>
            @if($case->requested_information)
                <tr>
                    <td class="p-2 font-bold border-r border-t border-slate-900 bg-slate-50 align-top">Requested Info / Actions:</td>
                    <td colspan="3" class="p-3 border-t border-slate-900 text-slate-900 leading-relaxed">
                        {{ $case->requested_information }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Section 3: Referrals & Interventions Log -->
    <div class="mb-5">
        <h3 class="text-xs font-bold uppercase tracking-wider bg-slate-100 border border-slate-900 px-3 py-1 text-slate-900 font-sans">
            III. Interventions, Endorsements & Referrals
        </h3>
        <table class="w-full text-xs border border-t-0 border-slate-900">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-900">
                    <th class="p-2 text-left font-bold border-r border-slate-900 w-28">Referral Date</th>
                    <th class="p-2 text-left font-bold border-r border-slate-900 w-32">Referral Type</th>
                    <th class="p-2 text-left font-bold border-r border-slate-900">Referred Unit / Program</th>
                    <th class="p-2 text-left font-bold">Endorsement Justification</th>
                </tr>
            </thead>
            <tbody>
                @forelse($case->referrals as $ref)
                    @php
                        $dest = $ref->referral_type === 'scholarship' 
                            ? ($ref->scholarship->name ?? 'Scholarship Program') 
                            : ($ref->referred_to_office ?? 'Campus Office');
                    @endphp
                    <tr class="border-b border-slate-900">
                        <td class="p-2 border-r border-slate-900">{{ $ref->created_at->format('M d, Y') }}</td>
                        <td class="p-2 border-r border-slate-900 uppercase font-semibold">{{ str_replace('_', ' ', $ref->referral_type) }}</td>
                        <td class="p-2 border-r border-slate-900 font-bold">{{ $dest }}</td>
                        <td class="p-2">{{ $ref->referral_note ?? 'No specific notes.' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 text-center text-slate-400 italic">No external referrals recorded for this case. Handled internally by OSDW.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Section 4: Attached Documents Checklist -->
    <div class="mb-8">
        <h3 class="text-xs font-bold uppercase tracking-wider bg-slate-100 border border-slate-900 px-3 py-1 text-slate-900 font-sans">
            IV. Supporting Verification Documents
        </h3>
        <div class="border border-t-0 border-slate-900 p-3 text-xs">
            @if($case->documents->count() > 0)
                <ul class="list-disc list-inside space-y-1 text-slate-800">
                    @foreach($case->documents as $doc)
                        <li>
                            <span class="font-semibold">{{ $doc->file_name ?? 'Attachment ' . ($loop->index + 1) }}</span>
                            <span class="text-slate-500">({{ $doc->created_at->format('M d, Y') }})</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-slate-400 italic">No document attachments uploaded for this case intake.</p>
            @endif
        </div>
    </div>

    <!-- Signatory Block -->
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

<x-app-layout>
    <x-slot name="title">Grantee Renewals & Requirements | CSU–Lal-lo OSDW</x-slot>

    <div class="space-y-6" x-data="{
        activeTab: 'requests',
        showRequestModal:  false,
        showHistoryModal:  false,
        showResubmitModal: false,
        resubmitRenewalId: '',
        resubmitStudentName: '',
        activeScholarshipId:   '',
        activeScholarshipName: '',
        presetDocuments: [
            'Certificate of Enrollment (COE)',
            'Certificate of Grades (COG)',
            'Certificate of Registration (COR)',
            'Photocopy of Student ID Card',
            'Statement of Account / Official Receipt',
            'Barangay Certificate of Indigency',
            'Good Moral Certificate',
            'Solo Parent / PWD ID'
        ],
        selectedDocs: ['Certificate of Enrollment (COE)', 'Photocopy of Student ID Card'],
        docInstructions: {},
        customDoc: '',
        submissionFilter: 'all',
        toggleDoc(doc) {
            let i = this.selectedDocs.indexOf(doc);
            i > -1 ? this.selectedDocs.splice(i,1) : this.selectedDocs.push(doc);
        },
        addCustomDoc() {
            let d = this.customDoc.trim();
            if (d && !this.presetDocuments.includes(d)) this.presetDocuments.push(d);
            if (d && !this.selectedDocs.includes(d)) this.selectedDocs.push(d);
            this.customDoc = '';
        },
        openRequest(id, name) { this.activeScholarshipId = id; this.activeScholarshipName = name; this.showRequestModal = true; },
        openHistory(id, name) { this.activeScholarshipId = id; this.activeScholarshipName = name; this.showHistoryModal = true; },
        openResubmit(id, name){ this.resubmitRenewalId = id; this.resubmitStudentName = name; this.showResubmitModal = true; }
    }">
        

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs font-bold px-4 py-3 rounded-2xl space-y-1 shadow-xs">
            @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
        </div>
        @endif

        {{-- Page Header & Tab Controls --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-[#7B1113] text-[#FFC107] flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#3B060F] tracking-tight">Grantee Renewals & Requirements</h1>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Configure semester renewal document requirements and verify grantee submissions.</p>
                </div>
            </div>

            {{-- 2-Tab Navigation Controls --}}
            <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shrink-0 text-xs font-extrabold">
                <button type="button" @click="activeTab = 'requests'"
                        :class="activeTab === 'requests' ? 'bg-white text-[#7B1113] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2 cursor-pointer">
                    <span>📋 Program Requirements</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-800" x-text="'{{ $scholarships->count() }}'"></span>
                </button>
                <button type="button" @click="activeTab = 'submissions'"
                        :class="activeTab === 'submissions' ? 'bg-white text-[#7B1113] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 rounded-xl transition flex items-center gap-2 cursor-pointer">
                    <span>📥 Submissions Queue</span>
                    @php
                        $pendingCount = $submittedRenewals->where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500 text-white font-black animate-pulse">{{ $pendingCount }}</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-800">{{ $submittedRenewals->count() }}</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- TAB 1: Program Requirement Checklists & Dispatch --}}
        <div x-show="activeTab === 'requests'" x-transition class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#3B060F] uppercase tracking-wider">Scholarship Program Checklists</h2>
                        <p class="text-xs text-slate-500">Configure renewal requirements and view active requirement status per program.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                        {{ $scholarships->count() }} {{ Str::plural('Program', $scholarships->count()) }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6">School Year</th>
                                <th class="py-3.5 px-4">Scholarship Program</th>
                                <th class="py-3.5 px-4 text-center">Total Grantees</th>
                                <th class="py-3.5 px-4 text-center">Approved Renewals</th>
                                <th class="py-3.5 px-4 text-center">Needs Resubmission</th>
                                <th class="py-3.5 px-4">Active Requirements</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($scholarships as $scholarship)
                            <tr class="hover:bg-slate-50/70 transition align-top">
                                <td class="py-3.5 px-4 sm:px-6 font-extrabold text-slate-900 whitespace-nowrap">
                                    {{ $scholarship->school_year ?? $scholarship->school_year_label ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $scholarship->name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $scholarship->provider ?? 'CSU OSDW' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-800 border border-slate-200">{{ $scholarship->total_grantees }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">{{ $scholarship->total_approved }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-200">{{ $scholarship->total_resubmit }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    @if($scholarship->active_requirements->isEmpty())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-400 border border-slate-200">None Configured</span>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                        @foreach($scholarship->active_requirements as $req)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                {{ $req->requirement_name }}{{ ($req->semester && $req->school_year) ? ' ('.$req->semester.')' : '' }}
                                            </span>
                                        @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-10 text-center text-slate-400 text-xs">No scholarship programs found. <a href="{{ route('admin.scholarships.create') }}" class="text-[#7B1113] font-bold underline">Create one</a>.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 2: Grantee Submissions & Verification Queue --}}
        <div x-show="activeTab === 'submissions'" x-transition class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#3B060F] uppercase tracking-wider">Grantee Submissions & Verification Queue</h2>
                        <p class="text-xs text-slate-500">Inspect uploaded student documents, verify eligibility, or request resubmission.</p>
                    </div>

                    {{-- Filter Sub-Tabs --}}
                    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold">
                        <button type="button" @click="submissionFilter = 'all'" :class="submissionFilter === 'all' ? 'bg-[#3B060F] text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1 rounded-lg transition">All ({{ $submittedRenewals->count() }})</button>
                        <button type="button" @click="submissionFilter = 'pending'" :class="submissionFilter === 'pending' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1 rounded-lg transition">Pending ({{ $submittedRenewals->where('status', 'pending')->count() }})</button>
                        <button type="button" @click="submissionFilter = 'verified'" :class="submissionFilter === 'verified' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1 rounded-lg transition">Verified ({{ $submittedRenewals->where('status', 'verified')->count() }})</button>
                        <button type="button" @click="submissionFilter = 'needs_resubmission'" :class="submissionFilter === 'needs_resubmission' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1 rounded-lg transition">Deficient ({{ $submittedRenewals->where('status', 'needs_resubmission')->count() }})</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6">Student Info</th>
                                <th class="py-3.5 px-4">Scholarship Program</th>
                                <th class="py-3.5 px-4">Requirement Type</th>
                                <th class="py-3.5 px-4">Submitted Date</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($submittedRenewals as $ren)
                            <tr x-show="submissionFilter === 'all' || submissionFilter === '{{ $ren->status }}'" class="hover:bg-slate-50/70 transition align-middle">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="font-extrabold text-slate-900">{{ $ren->scholar->student->user->full_name ?? 'Student' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $ren->scholar->student->student_number ?? 'N/A' }} • {{ $ren->scholar->student->course ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $ren->scholar->scholarship->name ?? 'Scholarship' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $ren->scholar->scholarship->provider ?? 'CSU OSDW' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                        {{ $ren->requirement_type ?? 'Renewal Requirement' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-medium">
                                    {{ $ren->submitted_at ? $ren->submitted_at->format('M d, Y') : $ren->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($ren->status === 'verified')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">Verified & Approved</span>
                                    @elseif($ren->status === 'needs_resubmission')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">Needs Resubmission</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">Pending Review</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        {{-- View Uploaded File --}}
                                        <a href="{{ route('admin.scholars.renewals.download', $ren->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-white hover:bg-slate-900 transition">
                                            👁️ View File
                                        </a>

                                        @if($ren->status !== 'verified')
                                            {{-- Approve Button --}}
                                            <form method="POST" action="{{ route('admin.grantees-requirements.verify-renewal', $ren->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition cursor-pointer">
                                                    ✓ Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($ren->status !== 'needs_resubmission')
                                            {{-- Resubmit Trigger --}}
                                            <button type="button" @click="openResubmit('{{ $ren->id }}', '{{ addslashes($ren->scholar->student->user->full_name ?? 'Student') }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition cursor-pointer">
                                                ⚠️ Resubmit
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-10 text-center text-slate-400 text-xs">No renewal document submissions recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL: Create Requirements --}}
        <div x-show="showRequestModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showRequestModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-[#3B060F]">Create Requirements</h3>
                                <p class="text-xs text-slate-500 font-medium" x-text="'Program: ' + activeScholarshipName"></p>
                            </div>
                        </div>
                        <button @click="showRequestModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form method="POST" action="{{ route('admin.grantees-requirements.request') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="req_scholarship_id" class="block text-xs font-bold text-slate-700 mb-1">Scholarship Program <span class="text-red-500">*</span></label>
                            <select id="req_scholarship_id" name="scholarship_id" x-model="activeScholarshipId" required class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-[#7B1113] focus:ring-[#7B1113]">
                                @foreach($scholarships as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }} (SY {{ $sp->school_year ?? 'Current' }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="req_school_year" class="block text-xs font-bold text-slate-700 mb-1">School Year (SY) <span class="text-red-500">*</span></label>
                                <input type="text" id="req_school_year" name="school_year" value="{{ date('Y').'-'.(date('Y')+1) }}" required
                                       pattern="^20\d{2}-20\d{2}$" placeholder="e.g. 2026-2027" title="Format: YYYY-YYYY"
                                       class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-[#7B1113] focus:ring-[#7B1113]">
                                <p class="text-[10px] text-slate-400 mt-0.5">Format: YYYY-YYYY</p>
                            </div>
                            <div>
                                <label for="req_semester" class="block text-xs font-bold text-slate-700 mb-1">Select Semester <span class="text-red-500">*</span></label>
                                <select id="req_semester" name="semester" required class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-[#7B1113] focus:ring-[#7B1113]">
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="req_deadline" class="block text-xs font-bold text-slate-700 mb-1">Submission Deadline <span class="text-red-500">*</span></label>
                            <input type="date" id="req_deadline" name="renewal_deadline" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-[#7B1113] focus:ring-[#7B1113]">
                            <p class="text-[10px] text-slate-400 mt-0.5">Grantees will be notified upon saving.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1.5">Requirements Need <span class="text-slate-400 font-normal">(Select using checkbox)</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 bg-slate-50 border border-slate-200 rounded-2xl p-3 max-h-48 overflow-y-auto">
                                <template x-for="doc in presetDocuments" :key="doc">
                                    <label class="flex items-center space-x-2 p-2 rounded-xl border border-transparent hover:border-slate-200 hover:bg-white transition cursor-pointer text-xs font-semibold text-slate-800">
                                        <input type="checkbox" name="documents[]" :value="doc" :checked="selectedDocs.includes(doc)" @change="toggleDoc(doc)" class="rounded border-slate-300 text-[#7B1113] focus:ring-[#7B1113]">
                                        <span x-text="doc"></span>
                                    </label>
                                </template>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="text" x-model="customDoc" @keydown.enter.prevent="addCustomDoc()" placeholder="+ Add custom document requirement" class="flex-1 text-xs font-medium rounded-xl border-slate-300 focus:border-[#7B1113] focus:ring-[#7B1113]">
                                <button type="button" @click="addCustomDoc()" class="px-3 py-2 text-xs font-extrabold bg-slate-800 hover:bg-slate-900 text-white rounded-xl transition shrink-0">+ Add</button>
                            </div>

                            <div x-show="selectedDocs.length > 0" class="mt-3 space-y-2">
                                <label class="block text-xs font-bold text-slate-800">Selected Requirements & Instructions <span class="text-slate-400 font-normal">(Optional instruction per item)</span></label>
                                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                    <template x-for="d in selectedDocs" :key="d">
                                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-extrabold text-[#3B060F]" x-text="d"></span>
                                                <button type="button" @click="toggleDoc(d)" class="text-[11px] text-red-600 hover:text-red-800 font-bold transition">✕ Remove</button>
                                            </div>
                                            <input type="text" 
                                                   :name="'instructions[' + d + ']'" 
                                                   x-model="docInstructions[d]" 
                                                   placeholder="Instructions for student (optional, e.g. Clear photocopy signed by Registrar)..." 
                                                   class="w-full text-xs font-medium rounded-lg border-slate-300 focus:border-[#7B1113] focus:ring-[#7B1113]">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" @click="showRequestModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 rounded-xl transition">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-[#7B1113] hover:bg-[#5a0c0e] text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Create Requirements
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: Completed History --}}
        <div x-show="showHistoryModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showHistoryModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-[#3B060F]">Completed Requirements History</h3>
                                <p class="text-xs text-slate-500 font-medium" x-text="'Program: ' + activeScholarshipName"></p>
                            </div>
                        </div>
                        <button @click="showHistoryModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    @foreach($scholarships as $sp)
                    <div x-show="activeScholarshipId == '{{ $sp->id }}'">
                        @if($sp->completed_requirements->isEmpty())
                        <div class="py-10 text-center text-xs text-slate-400 italic">No completed requirements yet for this program.</div>
                        @else
                        <div class="divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden">
                            @foreach($sp->completed_requirements as $req)
                            <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition text-xs">
                                <div>
                                    <div class="font-extrabold text-slate-900 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0"></span>{{ $req->requirement_name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 pl-4">{{ $req->semester && $req->school_year ? $req->semester.' SY '.$req->school_year.' | ' : '' }}{{ $req->deadline ? 'Deadline: '.$req->deadline->format('M d, Y').' | ' : '' }}Completed: {{ $req->updated_at->format('M d, Y') }}</div>
                                </div>
                                <form method="POST" action="{{ route('admin.grantees-requirements.update-status', $req->id) }}" class="shrink-0">@csrf @method('PATCH')<input type="hidden" name="status" value="active"><button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition cursor-pointer">Reactivate</button></form>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                    <div class="flex justify-end pt-2 border-t border-slate-100">
                        <button type="button" @click="showHistoryModal = false" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Request Resubmission Remarks --}}
        <div x-show="showResubmitModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showResubmitModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5" @click.stop>
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Request Document Resubmission</h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="'Student: ' + resubmitStudentName"></p>
                        </div>
                        <button @click="showResubmitModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form method="POST" :action="'/admin/grantees-requirements/renewals/' + resubmitRenewalId + '/verify'" class="space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="needs_resubmission">
                        <div>
                            <label for="resubmit_remarks" class="block text-xs font-bold text-slate-700 mb-1">Reason for Resubmission <span class="text-red-500">*</span></label>
                            <textarea id="resubmit_remarks" name="remarks" rows="3" required placeholder="e.g. Uploaded file is blurry or missing signature. Please upload clear scan." class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-amber-600 focus:ring-amber-600"></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" @click="showResubmitModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                Send Resubmission Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
